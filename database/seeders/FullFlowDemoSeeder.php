<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\AssistanceProgram;
use App\Models\AssistanceRequest;
use App\Models\BlockchainTransaction;
use App\Models\MerchantCategory;
use App\Models\MerchantProfile;
use App\Models\Settlement;
use App\Models\User;
use App\Services\ClaimValidationRuleService;
use App\Services\ProofBundleService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FullFlowDemoSeeder extends Seeder
{
    private const ACTIVE_REFERENCE = 'EDU-DEMO-ACTIVE-001';
    private const READY_REFERENCE = 'EDU-DEMO-READY-001';
    private const RELEASED_REFERENCE = 'EDU-DEMO-RELEASED-001';
    private const MERCHANT_CATEGORY = 'School Supplies';

    public function run(): void
    {
        $admin = $this->user('demo.admin@edunexus.test', 'Demo Cooperative Admin', 'admin');
        $member = $this->user('maria.santos@edunexus.test', 'Maria Santos', 'member');
        $merchantUser = $this->user('bookstore.partner@edunexus.test', 'Bookstore Partner User', 'merchant');
        $auditor = $this->user('demo.auditor@edunexus.test', 'Demo Cooperative Auditor', 'auditor');
        $merchantCategory = MerchantCategory::firstOrCreate(
            ['name' => self::MERCHANT_CATEGORY],
            [
                'slug' => Str::slug(self::MERCHANT_CATEGORY),
                'status' => 'Active',
            ]
        );

        $merchantProfile = MerchantProfile::updateOrCreate(
            ['user_id' => $merchantUser->id],
            [
                'business_name' => 'Lipa School Supplies Center',
                'merchant_category_id' => $merchantCategory->id,
                'merchant_category' => self::MERCHANT_CATEGORY,
                'contact_number' => '0917-555-0198',
                'address' => 'CM Recto Avenue, Lipa City, Batangas',
                'payout_account_name' => 'Lipa School Supplies Center',
                'payout_account_number' => '09175550198',
                'payout_qr' => 'demo-gcash-qr-lipa-school-supplies',
                'payout_notes' => 'Demo-safe payout account for simulated GCash reimbursement releases.',
                'status' => 'Active',
            ]
        );

        $program = AssistanceProgram::updateOrCreate(
            ['program_name' => 'Teaching Materials Assistance'],
            [
                'description' => 'Cooperative assistance for teacher classroom materials, books, and school supplies.',
                'merchant_category_id' => $merchantCategory->id,
                'merchant_category' => self::MERCHANT_CATEGORY,
                'maximum_amount' => 5000,
                'expiration_days' => 30,
                'status' => 'Active',
                'created_by' => $admin->id,
            ]
        );

        $pendingRequest = $this->pendingRequest($member, $program);
        $activeClaim = $this->approvedClaim(
            self::ACTIVE_REFERENCE,
            $member,
            $program,
            $admin,
            4000,
            'Demo approved active claim for merchant validation.'
        );
        $readyClaim = $this->processedClaim(
            self::READY_REFERENCE,
            $member,
            $program,
            $admin,
            $merchantUser,
            $merchantProfile,
            4500,
            'Pending',
            now()->subDay(),
            null
        );
        $releasedClaim = $this->processedClaim(
            self::RELEASED_REFERENCE,
            $member,
            $program,
            $admin,
            $merchantUser,
            $merchantProfile,
            5000,
            'Released',
            now()->subDays(3),
            now()->subDay()
        );

        $this->activityLog(
            'request_approved',
            'Assistance request approved',
            'Demo admin approved claim ' . self::ACTIVE_REFERENCE . ' for Maria Santos.',
            AssistanceRequest::class,
            $activeClaim->id,
            'Approved',
            $admin->id
        );
        $this->activityLog(
            'claim_processed',
            'Merchant processed assistance claim',
            'Lipa School Supplies Center validated and processed claim ' . self::READY_REFERENCE . '.',
            AssistanceRequest::class,
            $readyClaim->id,
            'Confirmed',
            $merchantUser->id
        );
        $this->activityLog(
            'settlement_completed',
            'Merchant settlement released',
            'Demo admin released settlement for claim ' . self::RELEASED_REFERENCE . '.',
            Settlement::class,
            Settlement::where('assistance_request_id', $releasedClaim->id)->value('id'),
            'Released',
            $admin->id
        );
        $this->activityLog(
            'blockchain_confirmed',
            'Morph proof recorded',
            'Morph proof record confirmed for claim ' . self::RELEASED_REFERENCE . '.',
            BlockchainTransaction::class,
            BlockchainTransaction::where('reference_code', self::RELEASED_REFERENCE)->value('id'),
            'Confirmed',
            $admin->id
        );

        $this->command?->newLine();
        $this->command?->info('EduNexUs full-flow demo data seeded.');
        $this->command?->newLine();
        $this->command?->line('Admin: demo.admin@edunexus.test / password');
        $this->command?->line('Member: maria.santos@edunexus.test / password');
        $this->command?->line('Merchant: bookstore.partner@edunexus.test / password');
        $this->command?->line('Auditor: demo.auditor@edunexus.test / password');
        $this->command?->newLine();
        $this->command?->line('References:');
        $this->command?->line(self::ACTIVE_REFERENCE . ' = active claim for merchant validation');
        $this->command?->line(self::READY_REFERENCE . ' = processed claim with pending settlement');
        $this->command?->line(self::RELEASED_REFERENCE . ' = released settlement with proof record');
        $this->command?->newLine();
        $this->command?->line('Pending request ID: ' . $pendingRequest->id . ' (no QR/reference until approval)');
    }

    private function user(string $email, string $name, string $role): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => 'password',
                'role' => $role,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
    }

    private function pendingRequest(User $member, AssistanceProgram $program): AssistanceRequest
    {
        $reason = 'Demo pending request for classroom lesson charts, paper, markers, and student activity sheets.';

        $request = AssistanceRequest::firstOrNew([
            'member_id' => $member->id,
            'program_id' => $program->id,
            'reason' => $reason,
            'status' => 'Pending',
        ]);

        $request->fill([
            'requested_amount' => 3500,
            'approved_amount' => null,
            'approval_date' => null,
            'expiration_date' => null,
            'reference_code' => null,
            'qr_code' => null,
            'approved_by' => null,
            'is_claimed' => false,
            'claimed_at' => null,
            'claimed_by' => null,
            'claim_status' => 'Unclaimed',
        ])->save();

        return $request;
    }

    private function approvedClaim(
        string $referenceCode,
        User $member,
        AssistanceProgram $program,
        User $admin,
        float $amount,
        string $reason
    ): AssistanceRequest {
        $claim = AssistanceRequest::updateOrCreate(
            ['reference_code' => $referenceCode],
            [
                'member_id' => $member->id,
                'program_id' => $program->id,
                'requested_amount' => $amount,
                'approved_amount' => $amount,
                'status' => 'Approved',
                'approval_date' => now()->subDays(2),
                'expiration_date' => now()->addDays(28),
                'approved_by' => $admin->id,
                'reason' => $reason,
                'is_claimed' => false,
                'claimed_at' => null,
                'claimed_by' => null,
                'claim_status' => 'Unclaimed',
            ]
        );

        $claim->update([
            'qr_code' => $this->qrPayload($claim),
        ]);

        return $claim;
    }

    private function processedClaim(
        string $referenceCode,
        User $member,
        AssistanceProgram $program,
        User $admin,
        User $merchantUser,
        MerchantProfile $merchantProfile,
        float $amount,
        string $settlementStatus,
        mixed $claimedAt,
        mixed $settledAt
    ): AssistanceRequest {
        $claim = $this->approvedClaim(
            $referenceCode,
            $member,
            $program,
            $admin,
            $amount,
            'Demo processed claim for settlement and Morph proof visibility.'
        );

        $ruleSummary = $this->validationSummary($claim, $merchantProfile);

        $claim->update([
            'is_claimed' => true,
            'claimed_at' => $claimedAt,
            'claimed_by' => $merchantUser->id,
            'claim_status' => 'Claimed',
        ]);

        $settlement = Settlement::updateOrCreate(
            ['assistance_request_id' => $claim->id],
            [
                'merchant_id' => $merchantUser->id,
                'amount' => $claim->approved_amount,
                'total_released' => $settlementStatus === 'Released' ? $claim->approved_amount : 0,
                'remaining_balance' => $settlementStatus === 'Released' ? 0 : $claim->approved_amount,
                'status' => $settlementStatus,
                'settled_at' => $settledAt,
                'last_released_at' => $settledAt,
            ]
        );

        $this->proofRecord($claim, $merchantProfile, $settlement, $ruleSummary);

        return $claim;
    }

    private function validationSummary(AssistanceRequest $claim, MerchantProfile $merchantProfile): array
    {
        $rules = app(ClaimValidationRuleService::class)->evaluate($claim, $merchantProfile);

        return app(ClaimValidationRuleService::class)->summary($rules);
    }

    private function proofRecord(
        AssistanceRequest $claim,
        MerchantProfile $merchantProfile,
        Settlement $settlement,
        array $ruleSummary
    ): BlockchainTransaction {
        $claim->loadMissing(['member', 'program']);

        $proofBundle = app(ProofBundleService::class)->claimProcessedBundle(
            $claim,
            $merchantProfile,
            $ruleSummary
        );

        $proofBundle['settlement_status'] = $settlement->status;
        $proofBundle['timestamp'] = optional($claim->claimed_at)->toIso8601String() ?? now()->toIso8601String();

        $proofHash = app(ProofBundleService::class)->hash($proofBundle);
        $passed = collect($ruleSummary)->where('passed', true)->count();
        $failed = collect($ruleSummary)->where('passed', false)->count();

        return BlockchainTransaction::updateOrCreate(
            [
                'transaction_type' => 'Claim',
                'reference_id' => $claim->id,
            ],
            [
                'reference_code' => $claim->reference_code,
                'transaction_hash' => '0x' . substr(hash('sha256', $claim->reference_code . '|morph-demo-transaction'), 0, 64),
                'blockchain_status' => 'Confirmed',
                'payload' => json_encode([
                    'event_type' => 'CLAIM_PROCESSED',
                    'reference_code' => $claim->reference_code,
                    'assistance_request_id' => $claim->id,
                    'program_id' => $claim->program_id,
                    'merchant_id' => $merchantProfile->user_id,
                    'member_id' => $claim->member_id,
                    'claim_amount' => $claim->approved_amount,
                    'approved_amount' => $claim->approved_amount,
                    'merchant_category' => $merchantProfile->merchant_category,
                    'program_category' => $claim->program->merchant_category,
                    'proof_hash' => $proofHash,
                    'proof_bundle' => $proofBundle,
                    'validation_rules' => $ruleSummary,
                    'validation_summary' => [
                        'passed' => $passed,
                        'failed' => $failed,
                        'all_passed' => $failed === 0,
                    ],
                    'settlement_status' => $settlement->status,
                    'status' => 'Claimed',
                    'timestamp' => $proofBundle['timestamp'],
                    'blockchain_error' => null,
                ]),
                'recorded_at' => $claim->claimed_at ?? now(),
            ]
        );
    }

    private function activityLog(
        string $eventType,
        string $title,
        string $description,
        string $referenceType,
        ?int $referenceId,
        string $status,
        int $userId
    ): void {
        ActivityLog::updateOrCreate(
            [
                'event_type' => $eventType,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ],
            [
                'user_id' => $userId,
                'title' => $title,
                'description' => $description,
                'status' => $status,
            ]
        );
    }

    private function qrPayload(AssistanceRequest $claim): string
    {
        return json_encode([
            'reference_code' => $claim->reference_code,
            'request_id' => $claim->id,
            'member_id' => $claim->member_id,
            'program_id' => $claim->program_id,
            'status' => 'Approved',
        ]);
    }
}
