<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\BlockchainTransaction;
use App\Models\Settlement;
use App\Services\ClaimValidationRuleService;
use App\Services\MorphBlockchainService;
use App\Services\ActivityLogService;
use App\Services\ProofBundleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Notifications\ClaimProcessedNotification;

class ClaimValidationController extends Controller
{
    public function index()
    {
        return view('merchant.claims.index');
    }

    public function verify(Request $request, ClaimValidationRuleService $ruleService)
    {
        $referenceCode = $this->normalizeReferenceCode($request->reference_code);

        if (! $referenceCode) {
            return redirect()
                ->route('merchant.claims.index')
                ->with('error', 'Reference code is required.');
        }

        $assistanceRequest = AssistanceRequest::with(['member', 'program'])
            ->where('reference_code', $referenceCode)
            ->first();

        if (! $assistanceRequest) {
            return back()->with(
                'error',
                'No assistance claim found for this reference code.'
            );
        }

        $rules = $ruleService->evaluate($assistanceRequest, auth()->user()->merchantProfile);

        return view('merchant.claims.verify', [
            'request' => $assistanceRequest,
            'rules' => $rules,
        ]);
    }

    public function process(
        AssistanceRequest $assistanceRequest,
        MorphBlockchainService $blockchainService,
        ClaimValidationRuleService $ruleService,
        ProofBundleService $proofBundleService
    ) {
        $assistanceRequest->loadMissing(['member', 'program']);

        $merchantProfile = auth()->user()->merchantProfile;
        $rules = $ruleService->evaluate($assistanceRequest, $merchantProfile);

        if (! $ruleService->allPassed($rules)) {
            return back()->with(
                'error',
                $ruleService->firstFailureMessage($rules) ?? 'Claim failed programmable validation.'
            );
        }

        $assistanceRequest->update([
            'is_claimed' => true,
            'claimed_at' => now(),
            'claimed_by' => auth()->id(),
            'claim_status' => 'Claimed',
        ]);

        $assistanceRequest->member->notify(
            new ClaimProcessedNotification($assistanceRequest)
        );

        Settlement::firstOrCreate(
            [
                'assistance_request_id' => $assistanceRequest->id,
            ],
            [
                'merchant_id' => auth()->id(),
                'amount' => $assistanceRequest->approved_amount,
                'total_released' => 0,
                'remaining_balance' => $assistanceRequest->approved_amount,
                'status' => 'Pending',
            ]
        );

        Log::info('Calling Morph claim proof service', [
            'assistance_request_id' => $assistanceRequest->id,
            'reference_code' => $assistanceRequest->reference_code,
            'merchant_id' => auth()->id(),
        ]);

        try {
            $blockchainResult = $blockchainService->recordClaimProof(
                $assistanceRequest->reference_code,
                (float) $assistanceRequest->approved_amount,
                auth()->id()
            );
        } catch (\Throwable $exception) {
            Log::error('Morph claim proof service failed before returning a result', [
                'assistance_request_id' => $assistanceRequest->id,
                'reference_code' => $assistanceRequest->reference_code,
                'merchant_id' => auth()->id(),
                'exception' => $exception->getMessage(),
            ]);

            $blockchainResult = [
                'success' => false,
                'transaction_hash' => null,
                'error' => $exception->getMessage(),
            ];
        }

        $ruleSummary = $ruleService->summary($rules);
        $proofBundle = $proofBundleService->claimProcessedBundle(
            $assistanceRequest,
            $merchantProfile,
            $ruleSummary
        );
        $proofHash = $proofBundleService->hash($proofBundle);

        BlockchainTransaction::create([
            'transaction_type' => 'Claim',
            'reference_id' => $assistanceRequest->id,
            'reference_code' => $assistanceRequest->reference_code,
            'transaction_hash' => $blockchainResult['transaction_hash'],
            'blockchain_status' => $blockchainResult['success'] ? 'Confirmed' : 'Failed',
            'payload' => json_encode([
                'event_type' => 'CLAIM_PROCESSED',
                'reference_code' => $assistanceRequest->reference_code,
                'claim_amount' => $assistanceRequest->approved_amount,
                'merchant_id' => auth()->id(),
                'merchant_category' => $merchantProfile->merchant_category,
                'program_category' => $assistanceRequest->program->merchant_category,
                'proof_hash' => $proofHash,
                'proof_bundle' => $proofBundle,
                'validation_rules' => $ruleSummary,
                'validation_summary' => [
                    'passed' => collect($ruleSummary)->where('passed', true)->count(),
                    'failed' => collect($ruleSummary)->where('passed', false)->count(),
                    'all_passed' => true,
                ],
                'status' => 'Claimed',
                'blockchain_error' => $blockchainResult['error'],
            ]),
            'recorded_at' => now(),
        ]);

        ActivityLogService::record(
            'claim_processed',
            'Merchant processed assistance claim',
            'Merchant validated and processed claim ' . $assistanceRequest->reference_code . '.',
            AssistanceRequest::class,
            $assistanceRequest->id,
            $blockchainResult['success'] ? 'Confirmed' : 'Failed'
        );

        $redirect = redirect()->route('merchant.claims.index');

        if ($blockchainResult['success']) {
            return $redirect->with('success', 'Claim processed and recorded on Morph successfully.');
        }

        return $redirect
            ->with('warning', 'Claim processed and settlement created, but Morph proof recording failed. Review the verification logs for details.');
    }

    private function normalizeReferenceCode(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        if (is_array($decoded) && filled($decoded['reference_code'] ?? null)) {
            return trim((string) $decoded['reference_code']);
        }

        return $value;
    }
}
