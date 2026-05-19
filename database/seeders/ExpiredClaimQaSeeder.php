<?php

namespace Database\Seeders;

use App\Models\AssistanceProgram;
use App\Models\AssistanceRequest;
use App\Models\MerchantProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpiredClaimQaSeeder extends Seeder
{
    private const ACTIVE_REFERENCE = 'EDU-TEST-ACTIVE-001';
    private const EXPIRED_REFERENCE = 'EDU-TEST-EXPIRED-001';
    private const MERCHANT_CATEGORY = 'School Supplies';

    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'qa.admin@edunexus.test'],
            [
                'name' => 'QA Cooperative Admin',
                'password' => 'password',
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        $member = User::updateOrCreate(
            ['email' => 'qa.member@edunexus.test'],
            [
                'name' => 'QA Test Member',
                'password' => 'password',
                'role' => 'member',
                'status' => 'active',
            ]
        );

        $merchantUser = User::updateOrCreate(
            ['email' => 'qa.merchant@edunexus.test'],
            [
                'name' => 'QA School Supplies Merchant',
                'password' => 'password',
                'role' => 'merchant',
                'status' => 'active',
            ]
        );

        $merchantProfile = MerchantProfile::updateOrCreate(
            ['user_id' => $merchantUser->id],
            [
                'business_name' => 'EduNexUs QA School Supplies',
                'merchant_category' => self::MERCHANT_CATEGORY,
                'contact_number' => '0917-000-0001',
                'address' => 'QA Demo District, Philippines',
                'status' => 'Active',
            ]
        );

        $program = AssistanceProgram::updateOrCreate(
            ['program_name' => 'QA Education Supplies Assistance'],
            [
                'description' => 'Dedicated QA program for active and expired claim validation testing.',
                'merchant_category' => self::MERCHANT_CATEGORY,
                'maximum_amount' => 2500,
                'expiration_days' => 30,
                'status' => 'Active',
                'created_by' => $admin->id,
            ]
        );

        $activeClaim = $this->approvedClaim(
            self::ACTIVE_REFERENCE,
            $member,
            $program,
            $admin,
            now()->addDays(30),
            'QA active approved claim for programmable rule validation testing.'
        );

        $expiredClaim = $this->approvedClaim(
            self::EXPIRED_REFERENCE,
            $member,
            $program,
            $admin,
            now()->subDays(5),
            'QA expired approved claim for programmable rule validation testing.'
        );

        $this->command?->newLine();
        $this->command?->info('Expired Claim QA Seeder completed.');
        $this->command?->line('ACTIVE CLAIM:');
        $this->command?->line($activeClaim->reference_code);
        $this->command?->newLine();
        $this->command?->line('EXPIRED CLAIM:');
        $this->command?->line($expiredClaim->reference_code);
        $this->command?->newLine();
        $this->command?->line('MERCHANT:');
        $this->command?->line($merchantProfile->business_name . ' / ' . $merchantProfile->merchant_category);
        $this->command?->newLine();
        $this->command?->line('QA PURPOSE:');
        $this->command?->line('Use the active reference to confirm all programmable rules pass.');
        $this->command?->line('Use the expired reference to confirm the not_expired rule fails while the claim remains approved and unclaimed.');
    }

    private function approvedClaim(
        string $referenceCode,
        User $member,
        AssistanceProgram $program,
        User $admin,
        mixed $expirationDate,
        string $reason
    ): AssistanceRequest {
        $claim = AssistanceRequest::updateOrCreate(
            ['reference_code' => $referenceCode],
            [
                'member_id' => $member->id,
                'program_id' => $program->id,
                'requested_amount' => 1800,
                'approved_amount' => 1800,
                'status' => 'Approved',
                'approval_date' => now()->subDay(),
                'expiration_date' => $expirationDate,
                'approved_by' => $admin->id,
                'reason' => $reason,
                'is_claimed' => false,
                'claimed_at' => null,
                'claimed_by' => null,
                'claim_status' => 'Unclaimed',
            ]
        );

        $claim->update([
            'qr_code' => json_encode([
                'reference_code' => $claim->reference_code,
                'request_id' => $claim->id,
                'member_id' => $claim->member_id,
                'program_id' => $claim->program_id,
                'status' => 'Approved',
            ]),
        ]);

        return $claim;
    }
}
