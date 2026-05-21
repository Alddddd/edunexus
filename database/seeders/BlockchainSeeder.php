<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\BlockchainTransaction;
use App\Models\Settlement;
use App\Models\SettlementPayout;
use Illuminate\Database\Seeder;

class BlockchainSeeder extends Seeder
{
    use DemoSeederSupport;

    public function run(): void
    {
        foreach ([
            'EDU-DEMO-CLAIM-001',
            'EDU-DEMO-PARTIAL-001',
            'EDU-DEMO-RELEASED-001',
        ] as $referenceCode) {
            $this->claimProof($referenceCode);
        }

        SettlementPayout::with('settlement.assistanceRequest', 'settlement.merchant.merchantProfile')
            ->orderBy('released_at')
            ->get()
            ->each(fn (SettlementPayout $payout) => $this->settlementProof($payout));
    }

    private function claimProof(string $referenceCode): void
    {
        $request = $this->demoRequest($referenceCode)->loadMissing(['member', 'program']);
        $merchant = $request->claimed_by ? \App\Models\User::with('merchantProfile')->find($request->claimed_by) : null;
        $merchantProfile = $merchant?->merchantProfile;
        $rules = $this->validationRules(false);

        $proofBundle = [
            'reference_code' => $request->reference_code,
            'event_type' => 'CLAIM_PROCESSED',
            'assistance_request_id' => $request->id,
            'program_id' => $request->program_id,
            'merchant_id' => $merchant?->id,
            'member_id' => $request->member_id,
            'approved_amount' => (float) $request->approved_amount,
            'merchant_category' => $merchantProfile?->merchant_category,
            'validation_rules' => $rules,
            'status' => 'Claimed',
            'timestamp' => $request->claimed_at?->toIso8601String(),
        ];
        $proofHash = $this->proofHash($proofBundle);

        BlockchainTransaction::updateOrCreate(
            [
                'transaction_type' => 'Claim',
                'reference_id' => $request->id,
            ],
            [
                'reference_code' => $request->reference_code,
                'transaction_hash' => $this->morphHash($request->reference_code . '|claim-proof'),
                'blockchain_status' => $referenceCode === 'EDU-DEMO-CLAIM-001' ? 'Pending' : 'Confirmed',
                'payload' => json_encode([
                    'event_type' => 'CLAIM_PROCESSED',
                    'reference_code' => $request->reference_code,
                    'claim_amount' => (float) $request->approved_amount,
                    'merchant_id' => $merchant?->id,
                    'merchant_category' => $merchantProfile?->merchant_category,
                    'program_category' => $request->program->merchant_category,
                    'proof_hash' => $proofHash,
                    'proof_bundle' => $proofBundle,
                    'validation_rules' => $rules,
                    'validation_summary' => [
                        'passed' => count($rules),
                        'failed' => 0,
                        'all_passed' => true,
                    ],
                    'status' => 'Claimed',
                    'blockchain_error' => null,
                ]),
                'recorded_at' => $request->claimed_at,
                'created_at' => $request->claimed_at,
                'updated_at' => $request->claimed_at,
            ]
        );

        ActivityLog::updateOrCreate(
            [
                'event_type' => 'blockchain_confirmed',
                'reference_type' => BlockchainTransaction::class,
                'reference_id' => $request->id,
            ],
            [
                'user_id' => $merchant?->id,
                'title' => 'Morph claim proof recorded',
                'description' => 'Morph proof metadata recorded for claim ' . $request->reference_code . '.',
                'status' => $referenceCode === 'EDU-DEMO-CLAIM-001' ? 'Pending' : 'Confirmed',
                'created_at' => $request->claimed_at?->copy()->addMinutes(2),
                'updated_at' => $request->claimed_at?->copy()->addMinutes(2),
            ]
        );
    }

    private function settlementProof(SettlementPayout $payout): void
    {
        $settlement = $payout->settlement;
        $request = $settlement->assistanceRequest;
        $metadata = $payout->metadata ?? [];
        $eduxTransfer = [
            'edux_transfer_enabled' => (bool) ($metadata['edux_transfer_enabled'] ?? true),
            'edux_transfer_status' => $metadata['edux_transfer_status'] ?? 'success',
            'edux_transaction_hash' => $metadata['edux_transaction_hash'] ?? null,
            'edux_from' => $metadata['edux_from'] ?? null,
            'edux_to' => $metadata['edux_to'] ?? null,
            'edux_amount' => $metadata['edux_amount'] ?? '1',
            'edux_token_symbol' => $metadata['edux_token_symbol'] ?? 'EDUX',
            'edux_token_contract' => $metadata['edux_token_contract'] ?? null,
            'edux_block_number' => $metadata['edux_block_number'] ?? null,
            'edux_error' => $metadata['edux_error'] ?? null,
        ];

        $proofBundle = [
            'reference_code' => $request->reference_code,
            'event_type' => 'SETTLEMENT_RELEASED',
            'settlement_id' => $settlement->id,
            'settlement_reference' => $payout->settlement_reference,
            'payout_id' => $payout->id,
            'assistance_request_id' => $request->id,
            'merchant_id' => $settlement->merchant_id,
            'member_id' => $request->member_id,
            'peso_amount' => (float) $payout->amount,
            'settlement_total' => (float) $settlement->amount,
            'total_released' => (float) $payout->total_released_after,
            'remaining_balance' => (float) $payout->remaining_balance_after,
            'payout_type' => $payout->payout_type,
            'payout_channel' => 'GCash/PHP simulation',
            'payout_mode' => 'demo_safe_simulation',
            'payout_account_name_used' => $payout->payout_account_name_used,
            'payout_account_number_last4' => substr(preg_replace('/\D+/', '', (string) $payout->payout_account_number_used), -4),
            'settlement_rail' => 'ERC-20-compatible',
            'erc20_metadata' => [
                'denomination' => 'PHP',
                'token_standard' => 'ERC-20-compatible',
                'transfer_mode' => 'simulated PHP payout with real EDUX ERC-20 testnet proof metadata',
                'cashout_channel' => 'GCash simulation',
                'edux_transfer' => $eduxTransfer,
            ],
            'edux_transfer' => $eduxTransfer,
            'network' => 'Morph testnet',
            'status' => $settlement->status,
            'released_at' => $payout->released_at?->toIso8601String(),
            'timestamp' => $payout->released_at?->toIso8601String(),
        ];
        $proofHash = $this->proofHash($proofBundle);

        BlockchainTransaction::updateOrCreate(
            [
                'transaction_type' => 'Settlement',
                'reference_id' => $request->id,
            ],
            [
                'reference_code' => $request->reference_code,
                'transaction_hash' => $payout->transaction_hash,
                'blockchain_status' => 'Confirmed',
                'payload' => json_encode([
                    'event_type' => 'SETTLEMENT_RELEASED',
                    'reference_code' => $request->reference_code,
                    'settlement_id' => $settlement->id,
                    'payout_id' => $payout->id,
                    'settlement_reference' => $payout->settlement_reference,
                    'merchant_id' => $settlement->merchant_id,
                    'peso_amount' => (float) $payout->amount,
                    'settlement_total' => (float) $settlement->amount,
                    'total_released' => (float) $settlement->total_released,
                    'remaining_balance' => (float) $settlement->remaining_balance,
                    'payout_type' => $payout->payout_type,
                    'payout_channel' => 'GCash/PHP simulation',
                    'payout_mode' => 'demo_safe_simulation',
                    'settlement_rail' => 'ERC-20-compatible',
                    'network' => 'Morph testnet',
                    'edux_transfer' => $eduxTransfer,
                    'proof_hash' => $proofHash,
                    'proof_bundle' => $proofBundle,
                    'status' => $settlement->status,
                    'blockchain_error' => null,
                    'demo_safe_notice' => 'GCash/PHP disbursement is simulated. EDUX transfer metadata represents the Morph testnet settlement proof rail.',
                ]),
                'recorded_at' => $payout->released_at,
                'created_at' => $payout->released_at,
                'updated_at' => $payout->released_at,
            ]
        );

        $payout->update(['proof_hash' => $proofHash]);
    }
}
