<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\AssistanceProgram;
use App\Models\AssistanceRequest;
use App\Models\BlockchainTransaction;
use App\Models\MerchantCategory;
use App\Models\MerchantProfile;
use App\Models\Settlement;
use App\Models\SettlementPayout;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DemoResetSeeder extends Seeder
{
    use DemoSeederSupport;

    private Carbon $baseDate;

    public function run(): void
    {
        $this->baseDate = Carbon::create(2026, 5, 20, 9, 0, 0, 'Asia/Manila');

        DB::transaction(function () {
            $this->resetWorkflowTables();

            $users = $this->seedUsers();
            $categories = $this->seedCategories();
            $merchants = $this->seedMerchants($users, $categories);
            $programs = $this->seedPrograms($users['admin'], $categories);
            $requests = $this->seedRequests($users, $merchants, $programs);
            $settlements = $this->seedSettlements($requests, $merchants);

            $this->seedPayouts($settlements);
            $this->seedProofLogs($requests, $settlements);
            $this->seedActivityLogs($users, $requests, $settlements);
        });

        $this->command?->info('EduNexUs clean demo reset completed. Users were preserved and stable QA workflow data was rebuilt.');
        $this->command?->line('Admin: admin@edunexus.test / password');
        $this->command?->line('Auditor: auditor@edunexus.test / password');
        $this->command?->line('Member: ana.reyes@edunexus.test / password');
        $this->command?->line('Member: roberto.cruz@edunexus.test / password');
        $this->command?->line('Merchant: lipa.supplies@edunexus.test / password');
        $this->command?->line('Merchant: educare.bookstore@edunexus.test / password');
        $this->command?->line('Demo references: EDU-DEMO-QR-001, EDU-DEMO-CLAIM-001, EDU-DEMO-PARTIAL-001, EDU-DEMO-RELEASED-001');
    }

    private function resetWorkflowTables(): void
    {
        foreach ([
            'notifications',
            'activity_logs',
            'blockchain_transactions',
            'settlement_payouts',
            'settlements',
            'assistance_requests',
            'assistance_programs',
            'merchant_profiles',
            'merchant_categories',
        ] as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->delete();
            }
        }
    }

    private function seedUsers(): array
    {
        return [
            'admin' => $this->stableUser('admin@edunexus.test', 'Admin User', 'admin'),
            'auditor' => $this->stableUser('auditor@edunexus.test', 'Auditor User', 'auditor'),
            'ana' => $this->stableUser('ana.reyes@edunexus.test', 'Ana Reyes', 'member'),
            'roberto' => $this->stableUser('roberto.cruz@edunexus.test', 'Roberto Cruz', 'member'),
            'lipa' => $this->stableUser('lipa.supplies@edunexus.test', 'Lipa School Supplies Center Operator', 'merchant'),
            'educare' => $this->stableUser('educare.bookstore@edunexus.test', 'EduCare Bookstore Cashier', 'merchant'),
        ];
    }

    private function stableUser(string $email, string $name, string $role): User
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => 'password',
                'role' => $role,
                'status' => 'active',
            ]
        );

        $user->forceFill(['email_verified_at' => $this->baseDate->copy()->subDays(10)])->save();

        return $user;
    }

    private function seedCategories(): array
    {
        return [
            'school' => MerchantCategory::create([
                'name' => 'School Supplies',
                'slug' => Str::slug('School Supplies'),
                'description' => 'Accredited school supplies, books, learning kits, and classroom materials.',
                'status' => 'Active',
            ]),
            'medical' => MerchantCategory::create([
                'name' => 'Medical Assistance',
                'slug' => Str::slug('Medical Assistance'),
                'description' => 'Accredited medical assistance partners for medicines and basic healthcare support.',
                'status' => 'Active',
            ]),
        ];
    }

    private function seedMerchants(array $users, array $categories): array
    {
        return [
            'lipa' => MerchantProfile::create([
                'user_id' => $users['lipa']->id,
                'business_name' => 'Lipa School Supplies Center',
                'merchant_category_id' => $categories['school']->id,
                'merchant_category' => $categories['school']->name,
                'contact_number' => '09171234567',
                'address' => 'CM Recto Avenue, Lipa City, Batangas',
                'payout_account_name' => 'Lipa School Supplies Center',
                'payout_account_number' => '09171234567',
                'payout_qr' => 'demo/payout-qr/lipa-school-supplies-center.webp',
                'payout_notes' => 'Accredited demo merchant. Release to the registered GCash merchant account after settlement review.',
                'status' => 'Active',
            ]),
            'educare' => MerchantProfile::create([
                'user_id' => $users['educare']->id,
                'business_name' => 'EduCare Bookstore',
                'merchant_category_id' => $categories['school']->id,
                'merchant_category' => $categories['school']->name,
                'contact_number' => '09179876543',
                'address' => 'P. Torres Street, Batangas City',
                'payout_account_name' => 'EduCare Bookstore',
                'payout_account_number' => '09179876543',
                'payout_qr' => 'demo/payout-qr/educare-bookstore.webp',
                'payout_notes' => 'Accredited demo merchant. Use branch cashier account for cooperative reimbursement releases.',
                'status' => 'Active',
            ]),
        ];
    }

    private function seedPrograms(User $admin, array $categories): array
    {
        return [
            'education' => AssistanceProgram::create([
                'program_name' => 'Education Assistance',
                'description' => 'Active programmable support for classroom supplies and learning materials. QR and merchant validation are enabled.',
                'merchant_category_id' => $categories['school']->id,
                'merchant_category' => $categories['school']->name,
                'maximum_amount' => 5000,
                'expiration_days' => 30,
                'status' => 'Active',
                'created_by' => $admin->id,
            ]),
            'medical' => AssistanceProgram::create([
                'program_name' => 'Medical Assistance',
                'description' => 'Active medical assistance support for accredited healthcare-related partners.',
                'merchant_category_id' => $categories['medical']->id,
                'merchant_category' => $categories['medical']->name,
                'maximum_amount' => 8000,
                'expiration_days' => 21,
                'status' => 'Active',
                'created_by' => $admin->id,
            ]),
        ];
    }

    private function seedRequests(array $users, array $merchants, array $programs): array
    {
        $requests = [
            'qr' => $this->approvedRequest(
                'EDU-DEMO-QR-001',
                $users['ana'],
                $programs['education'],
                $users['admin'],
                4200,
                'Approved education assistance pass for QR validation QA. Merchant claim has not been processed.',
                $this->baseDate->copy()->subDays(2)
            ),
            'claim' => $this->approvedRequest(
                'EDU-DEMO-CLAIM-001',
                $users['roberto'],
                $programs['education'],
                $users['admin'],
                5000,
                'Merchant-validated classroom supplies claim ready for payout release QA.',
                $this->baseDate->copy()->subDays(5),
                $users['lipa'],
                $this->baseDate->copy()->subDays(4)->addHours(2)
            ),
            'partial' => $this->approvedRequest(
                'EDU-DEMO-PARTIAL-001',
                $users['ana'],
                $programs['education'],
                $users['admin'],
                5000,
                'Partially released bookstore reimbursement for learning kits.',
                $this->baseDate->copy()->subDays(8),
                $users['educare'],
                $this->baseDate->copy()->subDays(7)->addHours(2)
            ),
            'released' => $this->approvedRequest(
                'EDU-DEMO-RELEASED-001',
                $users['roberto'],
                $programs['education'],
                $users['admin'],
                5000,
                'Fully released school supplies settlement with confirmed Morph proof logs.',
                $this->baseDate->copy()->subDays(11),
                $users['lipa'],
                $this->baseDate->copy()->subDays(10)->addHours(2)
            ),
        ];

        foreach ($requests as $request) {
            $request->update(['qr_code' => $this->qrPayload($request)]);
        }

        return $requests;
    }

    private function approvedRequest(
        string $referenceCode,
        User $member,
        AssistanceProgram $program,
        User $admin,
        float $amount,
        string $reason,
        Carbon $approvedAt,
        ?User $merchant = null,
        ?Carbon $claimedAt = null
    ): AssistanceRequest {
        return AssistanceRequest::create([
            'member_id' => $member->id,
            'program_id' => $program->id,
            'requested_amount' => $amount,
            'approved_amount' => $amount,
            'status' => 'Approved',
            'approval_date' => $approvedAt,
            'expiration_date' => $approvedAt->copy()->addDays((int) $program->expiration_days),
            'reference_code' => $referenceCode,
            'qr_code' => null,
            'approved_by' => $admin->id,
            'reason' => $reason,
            'is_claimed' => filled($claimedAt),
            'claimed_at' => $claimedAt,
            'claimed_by' => $merchant?->id,
            'claim_status' => filled($claimedAt) ? 'Claimed' : 'Unclaimed',
            'created_at' => $approvedAt->copy()->subHours(6),
            'updated_at' => $claimedAt ?? $approvedAt,
        ]);
    }

    private function seedSettlements(array $requests, array $merchants): array
    {
        return [
            'claim' => Settlement::create([
                'assistance_request_id' => $requests['claim']->id,
                'merchant_id' => $merchants['lipa']->user_id,
                'amount' => 5000,
                'total_released' => 0,
                'remaining_balance' => 5000,
                'status' => 'Pending',
                'settled_at' => null,
                'last_released_at' => null,
                'created_at' => $requests['claim']->claimed_at,
                'updated_at' => $requests['claim']->claimed_at,
            ]),
            'partial' => Settlement::create([
                'assistance_request_id' => $requests['partial']->id,
                'merchant_id' => $merchants['educare']->user_id,
                'amount' => 5000,
                'total_released' => 2000,
                'remaining_balance' => 3000,
                'status' => 'Partially Released',
                'settled_at' => null,
                'last_released_at' => $this->baseDate->copy()->subDays(4),
                'created_at' => $requests['partial']->claimed_at,
                'updated_at' => $this->baseDate->copy()->subDays(4),
            ]),
            'released' => Settlement::create([
                'assistance_request_id' => $requests['released']->id,
                'merchant_id' => $merchants['lipa']->user_id,
                'amount' => 5000,
                'total_released' => 5000,
                'remaining_balance' => 0,
                'status' => 'Released',
                'settled_at' => $this->baseDate->copy()->subDays(2),
                'last_released_at' => $this->baseDate->copy()->subDays(2),
                'created_at' => $requests['released']->claimed_at,
                'updated_at' => $this->baseDate->copy()->subDays(2),
            ]),
        ];
    }

    private function seedPayouts(array $settlements): void
    {
        $this->payout($settlements['partial'], [
            'settlement_reference' => 'PAYOUT-DEMO-PARTIAL-001',
            'payout_type' => 'partial',
            'amount' => 2000,
            'total_released_after' => 2000,
            'remaining_balance_after' => 3000,
            'released_at' => $this->baseDate->copy()->subDays(4),
            'transaction_hash' => $this->morphHash('PAYOUT-DEMO-PARTIAL-001|settlement-proof'),
        ]);

        $this->payout($settlements['released'], [
            'settlement_reference' => 'PAYOUT-DEMO-RELEASED-001',
            'payout_type' => 'full',
            'amount' => 5000,
            'total_released_after' => 5000,
            'remaining_balance_after' => 0,
            'released_at' => $this->baseDate->copy()->subDays(2),
            'transaction_hash' => '0xeb40d69c6fe42a02ae24ef15c906fa33cc850b2aad2025718127e55d17d50279',
        ]);
    }

    private function payout(Settlement $settlement, array $data): SettlementPayout
    {
        $profile = $settlement->merchant->merchantProfile;
        $proofHash = $this->proofHash([
            'settlement_id' => $settlement->id,
            'settlement_reference' => $data['settlement_reference'],
            'amount' => $data['amount'],
            'transaction_hash' => $data['transaction_hash'],
        ]);

        return SettlementPayout::create([
            'settlement_id' => $settlement->id,
            'settlement_reference' => $data['settlement_reference'],
            'payout_type' => $data['payout_type'],
            'amount' => $data['amount'],
            'settlement_total' => $settlement->amount,
            'total_released_after' => $data['total_released_after'],
            'remaining_balance_after' => $data['remaining_balance_after'],
            'payout_channel' => 'GCash/PHP simulation',
            'settlement_rail' => 'ERC-20-compatible',
            'network' => 'Morph testnet',
            'payout_account_name_used' => $profile->payout_account_name,
            'payout_account_number_used' => $profile->payout_account_number,
            'payout_qr_used' => $profile->payout_qr,
            'payout_notes_used' => $profile->payout_notes,
            'transaction_hash' => $data['transaction_hash'],
            'blockchain_status' => 'Confirmed',
            'proof_hash' => $proofHash,
            'metadata' => [
                'edux_transfer_enabled' => true,
                'edux_transfer_status' => 'success',
                'edux_transaction_hash' => $data['transaction_hash'],
                'edux_from' => '0x46B3b86EDce9E024598e6f869a36059DF5D4f87D',
                'edux_to' => '0x1DfBFF883cF6C548a1a64020fC164B2061Ba6422',
                'edux_amount' => '1',
                'edux_token_symbol' => 'EDUX',
                'edux_token_contract' => env('EDUX_TOKEN_ADDRESS') ?: '0x0000000000000000000000000000000000ed0001',
                'edux_block_number' => 5542600 + $settlement->id,
                'edux_error' => null,
                'demo_safe_notice' => 'Demo-safe payout layer: PHP/GCash disbursement is simulated; EDUX records settlement proof metadata on Morph.',
            ],
            'released_at' => $data['released_at'],
            'created_at' => $data['released_at'],
            'updated_at' => $data['released_at'],
        ]);
    }

    private function seedProofLogs(array $requests, array $settlements): void
    {
        $this->claimProof($requests['claim'], 'Pending', null);
        $this->claimProof($requests['partial'], 'Confirmed', $this->morphHash('EDU-DEMO-PARTIAL-001|claim-proof'));
        $this->claimProof($requests['released'], 'Confirmed', $this->morphHash('EDU-DEMO-RELEASED-001|claim-proof'));

        $this->settlementProof($settlements['partial']);
        $this->settlementProof($settlements['released']);

        BlockchainTransaction::create([
            'transaction_type' => 'Diagnostics',
            'reference_id' => 0,
            'reference_code' => 'EDU-DEMO-FAILED-001',
            'transaction_hash' => null,
            'blockchain_status' => 'Failed',
            'payload' => json_encode([
                'event_type' => 'PROOF_RECORDING_FAILED',
                'reference_code' => 'EDU-DEMO-FAILED-001',
                'status' => 'Failed',
                'blockchain_error' => 'Demo failed proof record. No confirmed Morph hash should be displayed.',
            ]),
            'recorded_at' => $this->baseDate->copy()->subDay(),
            'created_at' => $this->baseDate->copy()->subDay(),
            'updated_at' => $this->baseDate->copy()->subDay(),
        ]);
    }

    private function claimProof(AssistanceRequest $request, string $status, ?string $transactionHash): void
    {
        $request->loadMissing(['member', 'program']);
        $merchantProfile = $request->claimed_by ? User::find($request->claimed_by)?->merchantProfile : null;
        $proofBundle = [
            'reference_code' => $request->reference_code,
            'event_type' => 'CLAIM_PROCESSED',
            'assistance_request_id' => $request->id,
            'program_id' => $request->program_id,
            'merchant_id' => $request->claimed_by,
            'member_id' => $request->member_id,
            'approved_amount' => (float) $request->approved_amount,
            'merchant_category' => $merchantProfile?->merchant_category,
            'program_category' => $request->program->merchant_category,
            'status' => 'Claimed',
            'timestamp' => $request->claimed_at?->toIso8601String(),
        ];

        BlockchainTransaction::create([
            'transaction_type' => 'Claim',
            'reference_id' => $request->id,
            'reference_code' => $request->reference_code,
            'transaction_hash' => $transactionHash,
            'blockchain_status' => $status,
            'payload' => json_encode([
                'event_type' => 'CLAIM_PROCESSED',
                'reference_code' => $request->reference_code,
                'claim_amount' => (float) $request->approved_amount,
                'merchant_id' => $request->claimed_by,
                'merchant_category' => $merchantProfile?->merchant_category,
                'program_category' => $request->program->merchant_category,
                'proof_hash' => $this->proofHash($proofBundle),
                'proof_bundle' => $proofBundle,
                'validation_rules' => $this->validationRules(true),
                'validation_summary' => [
                    'passed' => 5,
                    'failed' => 0,
                    'all_passed' => true,
                ],
                'status' => 'Claimed',
                'blockchain_error' => $status === 'Pending' ? 'Awaiting Morph confirmation. No confirmed hash assigned yet.' : null,
            ]),
            'recorded_at' => $request->claimed_at,
            'created_at' => $request->claimed_at,
            'updated_at' => $request->claimed_at,
        ]);
    }

    private function settlementProof(Settlement $settlement): void
    {
        $settlement->loadMissing(['assistanceRequest', 'merchant.merchantProfile', 'payouts']);
        $payout = $settlement->payouts()->latest('released_at')->first();

        if (! $payout) {
            return;
        }

        $proofBundle = [
            'reference_code' => $settlement->assistanceRequest->reference_code,
            'event_type' => 'SETTLEMENT_RELEASED',
            'settlement_id' => $settlement->id,
            'settlement_reference' => $payout->settlement_reference,
            'payout_id' => $payout->id,
            'assistance_request_id' => $settlement->assistance_request_id,
            'merchant_id' => $settlement->merchant_id,
            'member_id' => $settlement->assistanceRequest->member_id,
            'peso_amount' => (float) $payout->amount,
            'settlement_total' => (float) $settlement->amount,
            'total_released' => (float) $payout->total_released_after,
            'remaining_balance' => (float) $payout->remaining_balance_after,
            'payout_type' => $payout->payout_type,
            'payout_channel' => $payout->payout_channel,
            'settlement_rail' => $payout->settlement_rail,
            'network' => $payout->network,
            'status' => $settlement->status,
            'released_at' => $payout->released_at?->toIso8601String(),
        ];

        BlockchainTransaction::create([
            'transaction_type' => 'Settlement',
            'reference_id' => $settlement->assistance_request_id,
            'reference_code' => $settlement->assistanceRequest->reference_code,
            'transaction_hash' => $payout->transaction_hash,
            'blockchain_status' => 'Confirmed',
            'payload' => json_encode([
                'event_type' => 'SETTLEMENT_RELEASED',
                'reference_code' => $settlement->assistanceRequest->reference_code,
                'settlement_id' => $settlement->id,
                'payout_id' => $payout->id,
                'settlement_reference' => $payout->settlement_reference,
                'merchant_id' => $settlement->merchant_id,
                'peso_amount' => (float) $payout->amount,
                'settlement_total' => (float) $settlement->amount,
                'total_released' => (float) $settlement->total_released,
                'remaining_balance' => (float) $settlement->remaining_balance,
                'payout_type' => $payout->payout_type,
                'payout_channel' => $payout->payout_channel,
                'settlement_rail' => $payout->settlement_rail,
                'network' => $payout->network,
                'proof_hash' => $this->proofHash($proofBundle),
                'proof_bundle' => $proofBundle,
                'status' => $settlement->status,
                'blockchain_error' => null,
            ]),
            'recorded_at' => $payout->released_at,
            'created_at' => $payout->released_at,
            'updated_at' => $payout->released_at,
        ]);
    }

    private function seedActivityLogs(array $users, array $requests, array $settlements): void
    {
        $this->activity('request_approved', 'Assistance request approved', 'Admin approved ' . $requests['qr']->reference_code . ' for Ana Reyes.', AssistanceRequest::class, $requests['qr']->id, 'Approved', $users['admin']->id, $requests['qr']->approval_date);
        $this->activity('claim_processed', 'Merchant processed assistance claim', 'Lipa School Supplies Center validated ' . $requests['claim']->reference_code . '.', AssistanceRequest::class, $requests['claim']->id, 'Claimed', $users['lipa']->id, $requests['claim']->claimed_at);
        $this->activity('settlement_ready', 'Settlement ready for release', 'Settlement for ' . $requests['claim']->reference_code . ' is eligible for payout release.', Settlement::class, $settlements['claim']->id, 'Pending', $users['admin']->id, $settlements['claim']->created_at);
        $this->activity('settlement_completed', 'Merchant payout partially released', 'Admin released PHP 2,000.00 for ' . $requests['partial']->reference_code . '.', Settlement::class, $settlements['partial']->id, 'Partially Released', $users['admin']->id, $settlements['partial']->last_released_at);
        $this->activity('settlement_completed', 'Merchant payout fully released', 'Admin released PHP 5,000.00 for ' . $requests['released']->reference_code . '.', Settlement::class, $settlements['released']->id, 'Released', $users['admin']->id, $settlements['released']->last_released_at);
    }

    private function activity(string $eventType, string $title, string $description, string $referenceType, int $referenceId, string $status, int $userId, Carbon $createdAt): void
    {
        ActivityLog::create([
            'event_type' => $eventType,
            'title' => $title,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'status' => $status,
            'user_id' => $userId,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }
}
