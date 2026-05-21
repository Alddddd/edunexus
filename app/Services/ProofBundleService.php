<?php

namespace App\Services;

use App\Models\AssistanceRequest;
use App\Models\MerchantProfile;
use App\Models\Settlement;
use App\Models\SettlementPayout;

class ProofBundleService
{
    public function claimProcessedBundle(
        AssistanceRequest $assistanceRequest,
        ?MerchantProfile $merchantProfile,
        array $validationRules
    ): array {
        $assistanceRequest->loadMissing(['member', 'program']);

        return [
            'reference_code' => $assistanceRequest->reference_code,
            'event_type' => 'CLAIM_PROCESSED',
            'assistance_request_id' => $assistanceRequest->id,
            'program_id' => $assistanceRequest->program_id,
            'merchant_id' => $merchantProfile?->user_id,
            'member_id' => $assistanceRequest->member_id,
            'approved_amount' => (float) $assistanceRequest->approved_amount,
            'merchant_category' => $merchantProfile?->merchant_category,
            'validation_rules' => $validationRules,
            'status' => 'Claimed',
            'timestamp' => now()->toIso8601String(),
        ];
    }

    public function hash(array $bundle): string
    {
        return hash('sha256', $this->canonicalJson($bundle));
    }

    public function settlementReleasedBundle(
        Settlement $settlement,
        string $settlementReference,
        ?SettlementPayout $payout = null,
        array $payoutContext = []
    ): array
    {
        $settlement->loadMissing([
            'assistanceRequest.member',
            'assistanceRequest.program',
            'merchant.merchantProfile',
        ]);

        $payoutMetadata = $payout?->metadata ?? [];
        $eduxTransfer = [
            'edux_transfer_enabled' => (bool) ($payoutMetadata['edux_transfer_enabled'] ?? false),
            'edux_transfer_status' => $payoutMetadata['edux_transfer_status'] ?? 'skipped',
            'edux_transaction_hash' => $payoutMetadata['edux_transaction_hash'] ?? null,
            'edux_from' => $payoutMetadata['edux_from'] ?? null,
            'edux_to' => $payoutMetadata['edux_to'] ?? null,
            'edux_amount' => $payoutMetadata['edux_amount'] ?? null,
            'edux_token_symbol' => $payoutMetadata['edux_token_symbol'] ?? 'EDUX',
            'edux_token_contract' => $payoutMetadata['edux_token_contract'] ?? null,
            'edux_block_number' => $payoutMetadata['edux_block_number'] ?? null,
            'edux_error' => $payoutMetadata['edux_error'] ?? null,
        ];

        return [
            'reference_code' => $settlement->assistanceRequest?->reference_code,
            'event_type' => 'SETTLEMENT_RELEASED',
            'settlement_id' => $settlement->id,
            'settlement_reference' => $settlementReference,
            'payout_id' => $payout?->id,
            'assistance_request_id' => $settlement->assistance_request_id,
            'merchant_id' => $settlement->merchant_id,
            'member_id' => $settlement->assistanceRequest?->member_id,
            'peso_amount' => (float) ($payout?->amount ?? $payoutContext['amount'] ?? $settlement->amount),
            'settlement_total' => (float) $settlement->amount,
            'total_released' => (float) ($payout?->total_released_after ?? $settlement->total_released ?? 0),
            'remaining_balance' => (float) ($payout?->remaining_balance_after ?? $settlement->remaining_balance ?? 0),
            'payout_type' => $payout?->payout_type ?? $payoutContext['payout_type'] ?? 'full',
            'payout_channel' => 'GCash/PHP simulation',
            'payout_mode' => 'demo_safe_simulation',
            'payout_account_name_used' => $payout?->payout_account_name_used ?? $settlement->merchant?->merchantProfile?->payout_account_name,
            'payout_account_number_last4' => $this->lastFour($payout?->payout_account_number_used ?? $settlement->merchant?->merchantProfile?->payout_account_number),
            'payout_qr_used' => $payout?->payout_qr_used,
            'payout_notes_used' => $payout?->payout_notes_used,
            'settlement_rail' => 'ERC-20-compatible',
            'erc20_metadata' => [
                'denomination' => 'PHP',
                'token_standard' => 'ERC-20-compatible',
                'transfer_mode' => 'off-chain PHP payout with on-chain proof metadata',
                'cashout_channel' => 'GCash simulation',
                'edux_transfer' => $eduxTransfer,
            ],
            'edux_transfer' => $eduxTransfer,
            'network' => 'Morph testnet',
            'status' => $settlement->status,
            'settled_at' => optional($settlement->settled_at)->toIso8601String(),
            'released_at' => optional($payout?->released_at ?? $settlement->last_released_at)->toIso8601String(),
            'timestamp' => now()->toIso8601String(),
        ];
    }

    private function lastFour(?string $value): ?string
    {
        $normalized = preg_replace('/\D+/', '', (string) $value);

        return $normalized ? substr($normalized, -4) : null;
    }

    private function canonicalJson(array $value): string
    {
        return json_encode(
            $this->sortKeys($value),
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION
        );
    }

    private function sortKeys(array $value): array
    {
        if (! array_is_list($value)) {
            ksort($value);
        }

        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $value[$key] = $this->sortKeys($item);
            }
        }

        return $value;
    }
}
