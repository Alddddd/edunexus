<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Settlement;
use App\Models\SettlementPayout;
use App\Notifications\SettlementCompletedNotification;
use Illuminate\Database\Seeder;

class SettlementSeeder extends Seeder
{
    use DemoSeederSupport;

    public function run(): void
    {
        $admin = $this->demoMember('admin@edunexus.test');

        $this->pendingSettlement(
            'EDU-DEMO-CLAIM-001',
            'lipa.supplies@edunexus.test',
            now()->subDays(3)->addMinutes(8)
        );

        $partialSettlement = $this->settlementWithPayout(
            'EDU-DEMO-PARTIAL-001',
            'educare.bookstore@edunexus.test',
            'Partially Released',
            2000,
            3000,
            [
                [
                    'reference' => 'PAYOUT-20260520-000002-01',
                    'type' => 'partial',
                    'amount' => 2000,
                    'released_after' => 2000,
                    'remaining_after' => 3000,
                    'released_at' => now()->subDays(4),
                    'edux_hash' => $this->morphHash('edux-partial-001'),
                    'edux_block' => 5542712,
                ],
            ]
        );

        $releasedSettlement = $this->settlementWithPayout(
            'EDU-DEMO-RELEASED-001',
            'health.pharmacy@edunexus.test',
            'Released',
            6500,
            0,
            [
                [
                    'reference' => 'PAYOUT-20260520-000003-01',
                    'type' => 'full',
                    'amount' => 6500,
                    'released_after' => 6500,
                    'remaining_after' => 0,
                    'released_at' => now()->subDays(2),
                    'edux_hash' => '0xeb40d69c6fe42a02ae24ef15c906fa33cc850b2aad2025718127e55d17d50279',
                    'edux_block' => 5542663,
                ],
            ]
        );

        $this->settlementActivity($partialSettlement, $admin->id, 'Partially Released');
        $this->settlementActivity($releasedSettlement, $admin->id, 'Released');

        $partialSettlement->merchant->notify(new SettlementCompletedNotification($partialSettlement));
        $releasedSettlement->merchant->notify(new SettlementCompletedNotification($releasedSettlement));
    }

    private function pendingSettlement(string $referenceCode, string $merchantEmail, $createdAt): Settlement
    {
        $request = $this->demoRequest($referenceCode);
        $merchant = $this->demoMerchant($merchantEmail);

        return Settlement::updateOrCreate(
            ['assistance_request_id' => $request->id],
            [
                'merchant_id' => $merchant->id,
                'amount' => $request->approved_amount,
                'total_released' => 0,
                'remaining_balance' => $request->approved_amount,
                'status' => 'Pending',
                'settled_at' => null,
                'last_released_at' => null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]
        );
    }

    private function settlementWithPayout(string $referenceCode, string $merchantEmail, string $status, float $released, float $remaining, array $payouts): Settlement
    {
        $request = $this->demoRequest($referenceCode);
        $merchant = $this->demoMerchant($merchantEmail);
        $latestPayoutAt = collect($payouts)->max('released_at');

        $settlement = Settlement::updateOrCreate(
            ['assistance_request_id' => $request->id],
            [
                'merchant_id' => $merchant->id,
                'amount' => $request->approved_amount,
                'total_released' => $released,
                'remaining_balance' => $remaining,
                'status' => $status,
                'settled_at' => $status === 'Released' ? $latestPayoutAt : null,
                'last_released_at' => $latestPayoutAt,
                'created_at' => $request->claimed_at,
                'updated_at' => $latestPayoutAt,
            ]
        );

        foreach ($payouts as $payout) {
            $this->payout($settlement, $payout);
        }

        return $settlement->refresh();
    }

    private function payout(Settlement $settlement, array $payout): SettlementPayout
    {
        $profile = $settlement->merchant->merchantProfile;
        $edux = [
            'edux_transfer_enabled' => true,
            'edux_transfer_status' => 'success',
            'edux_transaction_hash' => $payout['edux_hash'],
            'edux_from' => '0x46B3b86EDce9E024598e6f869a36059DF5D4f87D',
            'edux_to' => '0x1DfBFF883cF6C548a1a64020fC164B2061Ba6422',
            'edux_amount' => '1',
            'edux_token_symbol' => 'EDUX',
            'edux_token_contract' => env('EDUX_TOKEN_ADDRESS') ?: '0x0000000000000000000000000000000000ed0001',
            'edux_block_number' => $payout['edux_block'],
            'edux_error' => null,
        ];

        $metadata = [
            'payout_account_name_used' => $profile->payout_account_name,
            'payout_account_number_used' => $profile->payout_account_number,
            'payout_qr_used' => $profile->payout_qr,
            'payout_notes_used' => $profile->payout_notes,
            'demo_safe_notice' => 'Demo-safe payout layer: PHP/GCash disbursement is simulated; EDUX records real/testnet-ready settlement proof metadata.',
        ] + $edux;

        $proofHash = $this->proofHash([
            'settlement_id' => $settlement->id,
            'settlement_reference' => $payout['reference'],
            'amount' => $payout['amount'],
            'edux_transaction_hash' => $payout['edux_hash'],
        ]);

        return SettlementPayout::updateOrCreate(
            ['settlement_reference' => $payout['reference']],
            [
                'settlement_id' => $settlement->id,
                'payout_type' => $payout['type'],
                'amount' => $payout['amount'],
                'settlement_total' => $settlement->amount,
                'total_released_after' => $payout['released_after'],
                'remaining_balance_after' => $payout['remaining_after'],
                'payout_channel' => 'GCash/PHP simulation',
                'settlement_rail' => 'ERC-20-compatible',
                'network' => 'Morph testnet',
                'payout_account_name_used' => $profile->payout_account_name,
                'payout_account_number_used' => $profile->payout_account_number,
                'payout_qr_used' => $profile->payout_qr,
                'payout_notes_used' => $profile->payout_notes,
                'transaction_hash' => $this->morphHash($payout['reference'] . '|settlement-proof'),
                'blockchain_status' => 'Confirmed',
                'proof_hash' => $proofHash,
                'metadata' => $metadata,
                'released_at' => $payout['released_at'],
                'created_at' => $payout['released_at'],
                'updated_at' => $payout['released_at'],
            ]
        );
    }

    private function settlementActivity(Settlement $settlement, int $adminId, string $status): void
    {
        ActivityLog::updateOrCreate(
            [
                'event_type' => 'settlement_completed',
                'reference_type' => Settlement::class,
                'reference_id' => $settlement->id,
            ],
            [
                'user_id' => $adminId,
                'title' => 'Merchant payout released',
                'description' => 'Admin released PHP ' . number_format((float) $settlement->total_released, 2) . ' for settlement #' . $settlement->id . '.',
                'status' => $status,
                'created_at' => $settlement->last_released_at,
                'updated_at' => $settlement->last_released_at,
            ]
        );
    }
}
