<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockchainTransaction;
use App\Models\Settlement;
use App\Models\SettlementPayout;
use App\Services\ActivityLogService;
use App\Services\MorphBlockchainService;
use App\Services\ProofBundleService;
use App\Notifications\SettlementCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SettlementController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'status' => ['nullable', 'string', 'in:Pending,Partially Released,Released'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $stats = [
            'total' => Settlement::count(),
            'pending' => Settlement::where('status', 'Pending')->count(),
            'partially_released' => Settlement::where('status', 'Partially Released')->count(),
            'released' => Settlement::whereIn('status', ['Released', 'Settled'])->count(),
            'pending_amount' => Settlement::whereIn('status', ['Pending', 'Partially Released'])->sum('remaining_balance'),
            'settled_amount' => Settlement::whereIn('status', ['Released', 'Settled'])->sum('total_released'),
            'released_amount' => Settlement::sum('total_released'),
            'remaining_amount' => Settlement::sum('remaining_balance'),
            'total_amount' => Settlement::sum('amount'),
        ];

        $settlements = Settlement::with([
                'assistanceRequest.member',
                'assistanceRequest.program',
                'merchant.merchantProfile',
                'payouts' => fn ($query) => $query->latest('released_at')->latest('id'),
            ])
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('status', 'like', '%' . $search . '%')
                        ->orWhereHas('assistanceRequest', function ($query) use ($search) {
                            $query
                                ->where('reference_code', 'like', '%' . $search . '%')
                                ->orWhereHas('member', function ($query) use ($search) {
                                    $query->where('name', 'like', '%' . $search . '%');
                                });
                        })
                        ->orWhereHas('merchant', function ($query) use ($search) {
                            $query
                                ->where('name', 'like', '%' . $search . '%')
                                ->orWhereHas('merchantProfile', function ($query) use ($search) {
                                    $query
                                        ->where('business_name', 'like', '%' . $search . '%')
                                        ->orWhere('merchant_category', 'like', '%' . $search . '%');
                                });
                        });
                });
            })
            ->latest()
            ->latest('id')
            ->paginate(5)
            ->withQueryString();

        $proofRecords = BlockchainTransaction::query()
            ->where('transaction_type', 'Claim')
            ->whereIn('reference_id', $settlements->pluck('assistance_request_id')->filter())
            ->latest('recorded_at')
            ->latest('id')
            ->get()
            ->unique('reference_id')
            ->keyBy('reference_id');

        $settlementProofRecords = BlockchainTransaction::query()
            ->where('transaction_type', 'Settlement')
            ->whereIn('reference_id', $settlements->pluck('assistance_request_id')->filter())
            ->latest('recorded_at')
            ->latest('id')
            ->get()
            ->unique('reference_id')
            ->keyBy('reference_id');

        return view('admin.settlements.index', compact(
            'settlements',
            'filters',
            'stats',
            'proofRecords',
            'settlementProofRecords'
        ));
    }

 public function markAsSettled(
    Request $request,
    Settlement $settlement,
    MorphBlockchainService $blockchainService,
    ProofBundleService $proofBundleService
 )
{
    $settlement->loadMissing([
        'assistanceRequest.member',
        'assistanceRequest.program',
        'merchant.merchantProfile',
    ]);

    $remainingBalance = $settlement->computed_remaining_balance ?: (float) $settlement->amount;

    if (in_array($settlement->status, ['Released', 'Settled'], true) || $remainingBalance <= 0) {
        return back()->with('success', 'Settlement has already been fully released.');
    }

    if (! $this->isReleaseEligible($settlement)) {
        throw ValidationException::withMessages([
            'settlement' => 'Release payout is available only after a merchant-validated claim exists and the settlement still has an eligible balance.',
        ]);
    }

    $merchantProfile = $settlement->merchant?->merchantProfile;

    if (! $merchantProfile || blank($merchantProfile->payout_account_name) || blank($merchantProfile->payout_account_number)) {
        throw ValidationException::withMessages([
            'payout_details' => 'Merchant payout details are required before releasing settlement.',
        ]);
    }

    $validated = $request->validate([
        'payout_type' => ['required', 'in:full,partial'],
        'partial_amount' => ['nullable', 'numeric', 'min:0.01'],
    ]);

    $payoutAmount = $validated['payout_type'] === 'full'
        ? $remainingBalance
        : (float) ($validated['partial_amount'] ?? 0);

    if ($validated['payout_type'] === 'partial' && $payoutAmount <= 0) {
        throw ValidationException::withMessages([
            'partial_amount' => 'Enter a partial payout amount greater than zero.',
        ]);
    }

    if ($payoutAmount > $remainingBalance) {
        throw ValidationException::withMessages([
            'partial_amount' => 'Payout amount cannot exceed the remaining balance of PHP ' . number_format($remainingBalance, 2) . '.',
        ]);
    }

    $releasedAt = now();
    $settlementReference = 'PAYOUT-' . $releasedAt->format('Ymd') . '-' . str_pad((string) $settlement->id, 6, '0', STR_PAD_LEFT) . '-' . str_pad((string) (SettlementPayout::where('settlement_id', $settlement->id)->count() + 1), 2, '0', STR_PAD_LEFT);

    $payout = DB::transaction(function () use ($settlement, $settlementReference, $validated, $payoutAmount, $releasedAt, $merchantProfile) {
        $lockedSettlement = Settlement::query()
            ->whereKey($settlement->id)
            ->lockForUpdate()
            ->firstOrFail();

        $currentReleased = (float) ($lockedSettlement->total_released ?? 0);
        $settlementTotal = (float) $lockedSettlement->amount;
        $remainingBefore = max((float) ($lockedSettlement->remaining_balance ?? ($settlementTotal - $currentReleased)), 0);
        $releaseAmount = $validated['payout_type'] === 'full' ? $remainingBefore : $payoutAmount;

        if ($releaseAmount <= 0 || $releaseAmount > $remainingBefore) {
            throw ValidationException::withMessages([
                'partial_amount' => 'Payout amount must be within the current remaining settlement balance.',
            ]);
        }

        $totalReleasedAfter = round($currentReleased + $releaseAmount, 2);
        $remainingAfter = max(round($settlementTotal - $totalReleasedAfter, 2), 0);
        $status = $remainingAfter > 0 ? 'Partially Released' : 'Released';

        $lockedSettlement->update([
            'total_released' => $totalReleasedAfter,
            'remaining_balance' => $remainingAfter,
            'status' => $status,
            'last_released_at' => $releasedAt,
            'settled_at' => $status === 'Released' ? $releasedAt : $lockedSettlement->settled_at,
        ]);

        return SettlementPayout::create([
            'settlement_id' => $lockedSettlement->id,
            'settlement_reference' => $settlementReference,
            'payout_type' => $validated['payout_type'],
            'amount' => $releaseAmount,
            'settlement_total' => $settlementTotal,
            'total_released_after' => $totalReleasedAfter,
            'remaining_balance_after' => $remainingAfter,
            'payout_channel' => 'GCash/PHP simulation',
            'settlement_rail' => 'ERC-20-compatible',
            'network' => 'Morph testnet',
            'payout_account_name_used' => $merchantProfile->payout_account_name,
            'payout_account_number_used' => $merchantProfile->payout_account_number,
            'payout_qr_used' => $merchantProfile->payout_qr,
            'payout_notes_used' => $merchantProfile->payout_notes,
            'metadata' => [
                'payout_account_name_used' => $merchantProfile->payout_account_name,
                'payout_account_number_used' => $merchantProfile->payout_account_number,
                'payout_qr_used' => $merchantProfile->payout_qr,
                'payout_notes_used' => $merchantProfile->payout_notes,
                'demo_safe_notice' => 'Demo-safe payout layer: PHP/GCash disbursement is simulated to avoid requiring paid payout APIs or real-money transfers during judging. Settlement proof is still recorded through the ERC-20-compatible Morph settlement rail.',
            ],
            'released_at' => $releasedAt,
        ]);
    });

    $settlement->refresh()->loadMissing([
        'assistanceRequest.member',
        'assistanceRequest.program',
        'merchant.merchantProfile',
    ]);

    try {
        $eduxTransferResult = $blockchainService->transferSettlementToken();
    } catch (\Throwable $exception) {
        $eduxTransferResult = [
            'success' => false,
            'edux_transfer_enabled' => filter_var(env('EDUX_DEMO_TRANSFER_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
            'edux_transfer_status' => 'failed',
            'edux_error' => $exception->getMessage(),
        ];
    }

    $eduxMetadata = $this->eduxTransferMetadata($eduxTransferResult);
    $payout->update([
        'metadata' => array_merge($payout->metadata ?? [], $eduxMetadata),
    ]);
    $payout->refresh();

    $proofBundle = $proofBundleService->settlementReleasedBundle($settlement, $settlementReference, $payout);
    $proofHash = $proofBundleService->hash($proofBundle);

    Log::info('Calling Morph settlement proof service', [
        'settlement_id' => $settlement->id,
        'settlement_reference' => $settlementReference,
        'merchant_id' => $settlement->merchant_id,
        'payout_id' => $payout->id,
    ]);

    try {
        $blockchainResult = $blockchainService->recordSettlementProof(
            $settlementReference,
            (float) $payout->amount,
            (int) $settlement->merchant_id
        );
    } catch (\Throwable $exception) {
        Log::error('Morph settlement proof service failed before returning a result', [
            'settlement_id' => $settlement->id,
            'settlement_reference' => $settlementReference,
            'merchant_id' => $settlement->merchant_id,
            'payout_id' => $payout->id,
            'exception' => $exception->getMessage(),
        ]);

        $blockchainResult = [
            'success' => false,
            'transaction_hash' => null,
            'error' => $exception->getMessage(),
        ];
    }

    BlockchainTransaction::create([
        'transaction_type' => 'Settlement',
        'reference_id' => $settlement->assistance_request_id,
        'reference_code' => $settlement->assistanceRequest?->reference_code,
        'transaction_hash' => $blockchainResult['transaction_hash'],
        'blockchain_status' => $blockchainResult['success'] ? 'Confirmed' : 'Failed',
        'payload' => json_encode([
            'event_type' => 'SETTLEMENT_RELEASED',
            'reference_code' => $settlement->assistanceRequest?->reference_code,
            'settlement_id' => $settlement->id,
            'payout_id' => $payout->id,
            'settlement_reference' => $settlementReference,
            'merchant_id' => $settlement->merchant_id,
            'peso_amount' => (float) $payout->amount,
            'settlement_total' => (float) $settlement->amount,
            'total_released' => (float) $settlement->total_released,
            'remaining_balance' => (float) $settlement->remaining_balance,
            'payout_type' => $payout->payout_type,
            'payout_channel' => 'GCash/PHP simulation',
            'payout_mode' => 'demo_safe_simulation',
            'payout_account_name_used' => $payout->payout_account_name_used,
            'payout_account_number_last4' => $this->lastFour($payout->payout_account_number_used),
            'payout_qr_used' => $payout->payout_qr_used,
            'payout_notes_used' => $payout->payout_notes_used,
            'settlement_rail' => 'ERC-20-compatible',
            'erc20_metadata' => [
                'denomination' => 'PHP',
                'token_standard' => 'ERC-20-compatible',
                'transfer_mode' => 'off-chain PHP payout with on-chain proof metadata',
                'cashout_channel' => 'GCash simulation',
                'edux_transfer' => $eduxMetadata,
            ],
            'edux_transfer' => $eduxMetadata,
            'network' => 'Morph testnet',
            'proof_hash' => $proofHash,
            'proof_bundle' => $proofBundle,
            'status' => $settlement->status,
            'blockchain_error' => $blockchainResult['error'],
            'demo_safe_notice' => 'Demo-safe payout layer: PHP/GCash disbursement is simulated to avoid requiring paid payout APIs or real-money transfers during judging. Settlement proof is still recorded through the ERC-20-compatible Morph settlement rail.',
        ]),
        'recorded_at' => now(),
    ]);

    $payout->update([
        'transaction_hash' => $blockchainResult['transaction_hash'],
        'blockchain_status' => $blockchainResult['success'] ? 'Confirmed' : 'Failed',
        'proof_hash' => $proofHash,
        'metadata' => array_merge($payout->metadata ?? [], $eduxMetadata, [
            'morph_proof_transaction_hash' => $blockchainResult['transaction_hash'],
            'morph_proof_status' => $blockchainResult['success'] ? 'Confirmed' : 'Failed',
            'morph_proof_error' => $blockchainResult['error'] ?? null,
        ]),
    ]);

    $settlement->merchant->notify(
        new SettlementCompletedNotification($settlement)
    );

    ActivityLogService::record(
        'settlement_completed',
        'Merchant payout released',
        'Admin released PHP ' . number_format((float) $payout->amount, 2) . ' for merchant settlement #' . $settlement->id . '.',
        \App\Models\Settlement::class,
        $settlement->id,
        $blockchainResult['success'] ? 'Confirmed' : 'Failed'
    );

    $message = 'PHP payout released, demo-safe GCash metadata recorded, Morph proof logged, and merchant notified.';

    if (($eduxMetadata['edux_transfer_status'] ?? null) === 'success') {
        $message .= ' Real EDUX ERC-20 testnet transfer recorded.';
    } elseif (($eduxMetadata['edux_transfer_status'] ?? null) === 'failed') {
        return back()
            ->with('success', $message)
            ->with('warning', 'EDUX transfer failed safely. Payout math was preserved; review settlement metadata for details.');
    }

    return back()->with('success', $message);
}

private function eduxTransferMetadata(array $result): array
{
    $status = $result['edux_transfer_status']
        ?? (($result['receipt_status'] ?? null) === 'success' ? 'success' : 'failed');

    return [
        'edux_transfer_enabled' => (bool) ($result['edux_transfer_enabled'] ?? false),
        'edux_transfer_status' => $status,
        'edux_transaction_hash' => $result['edux_transaction_hash'] ?? $result['transaction_hash'] ?? null,
        'edux_from' => $result['edux_from'] ?? $result['from_address'] ?? null,
        'edux_to' => $result['edux_to'] ?? $result['to_address'] ?? null,
        'edux_amount' => $result['edux_amount'] ?? $result['token_amount'] ?? env('EDUX_DEMO_TRANSFER_AMOUNT', '1'),
        'edux_token_symbol' => $result['edux_token_symbol'] ?? $result['token_symbol'] ?? 'EDUX',
        'edux_token_contract' => $result['edux_token_contract'] ?? $result['token_contract'] ?? env('EDUX_TOKEN_ADDRESS'),
        'edux_block_number' => $result['edux_block_number'] ?? $result['block_number'] ?? null,
        'edux_error' => $result['edux_error'] ?? $result['error'] ?? null,
    ];
}

private function lastFour(?string $value): ?string
{
    $normalized = preg_replace('/\D+/', '', (string) $value);

    return $normalized ? substr($normalized, -4) : null;
}

private function isReleaseEligible(Settlement $settlement): bool
{
    $claim = $settlement->assistanceRequest;

    return $claim
        && $claim->status === 'Approved'
        && filled($claim->reference_code)
        && filled($claim->qr_code)
        && $claim->is_claimed
        && $claim->claimed_at
        && (int) $claim->claimed_by === (int) $settlement->merchant_id
        && in_array($claim->claim_status, ['Claimed', 'Processed'], true)
        && ! in_array($settlement->status, ['Released', 'Settled'], true)
        && $settlement->computed_remaining_balance > 0;
}

    
}
