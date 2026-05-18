<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\BlockchainTransaction;
use App\Models\Settlement;
use App\Services\MorphBlockchainService;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use App\Notifications\ClaimProcessedNotification;

class ClaimValidationController extends Controller
{
    public function index()
    {
        return view('merchant.claims.index');
    }

    public function verify(Request $request)
    {
        $referenceCode = $request->reference_code;

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

        $merchantProfile = auth()->user()->merchantProfile;

        $merchantCategoryAllowed =
            $merchantProfile &&
            $merchantProfile->status === 'Active' &&
            strtolower($merchantProfile->merchant_category) === strtolower($assistanceRequest->program->merchant_category);

        $rules = [
            [
                'label' => 'Request is approved',
                'passed' => $assistanceRequest->status === 'Approved',
                'description' => 'Only approved assistance requests can be claimed.',
            ],
            [
                'label' => 'Claim is not expired',
                'passed' => now()->lessThanOrEqualTo($assistanceRequest->expiration_date),
                'description' => 'The claim must still be within its validity period.',
            ],
            [
                'label' => 'Claim has not been used',
                'passed' => ! $assistanceRequest->is_claimed,
                'description' => 'Each assistance QR/reference can only be claimed once.',
            ],
            [
                'label' => 'Amount is within program limit',
                'passed' => $assistanceRequest->approved_amount <= $assistanceRequest->program->maximum_amount,
                'description' => 'Approved amount must not exceed the program maximum.',
            ],
            [
                'label' => 'Merchant category is allowed',
                'passed' => $merchantCategoryAllowed,
                'description' => 'Merchant must be active and match the assistance program category.',
            ],
        ];

        return view('merchant.claims.verify', [
            'request' => $assistanceRequest,
            'rules' => $rules,
        ]);
    }

    public function process(
        AssistanceRequest $assistanceRequest,
        MorphBlockchainService $blockchainService
    ) {
        if ($assistanceRequest->status !== 'Approved') {
            return back()->with('error', 'Claim is not approved.');
        }

        if ($assistanceRequest->is_claimed) {
            return back()->with('error', 'Claim already processed.');
        }

        if (now()->greaterThan($assistanceRequest->expiration_date)) {
            return back()->with('error', 'Claim has expired.');
        }

        $merchantProfile = auth()->user()->merchantProfile;

        if (
            ! $merchantProfile ||
            $merchantProfile->status !== 'Active' ||
            strtolower($merchantProfile->merchant_category) !== strtolower($assistanceRequest->program->merchant_category)
        ) {
            return back()->with('error', 'Merchant is not accredited for this assistance category.');
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
                'status' => 'Pending',
            ]
        );

        $blockchainResult = $blockchainService->recordClaimProof(
            $assistanceRequest->reference_code,
            (float) $assistanceRequest->approved_amount,
            auth()->id()
        );

        BlockchainTransaction::create([
            'transaction_type' => 'Claim',
            'reference_id' => $assistanceRequest->id,
            'reference_code' => $assistanceRequest->reference_code,
            'transaction_hash' => $blockchainResult['transaction_hash'],
            'blockchain_status' => $blockchainResult['success'] ? 'Confirmed' : 'Failed',
            'payload' => json_encode([
                'reference_code' => $assistanceRequest->reference_code,
                'claim_amount' => $assistanceRequest->approved_amount,
                'merchant_id' => auth()->id(),
                'merchant_category' => $merchantProfile->merchant_category,
                'program_category' => $assistanceRequest->program->merchant_category,
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

        return redirect()
            ->route('merchant.claims.verify', [
                'reference_code' => $assistanceRequest->reference_code,
            ])
            ->with(
                'success',
                $blockchainResult['success']
                    ? 'Claim processed and recorded on Morph successfully.'
                    : 'Claim processed, but blockchain recording failed.'
            );
    }
}