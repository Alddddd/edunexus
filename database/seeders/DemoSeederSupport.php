<?php

namespace Database\Seeders;

use App\Models\AssistanceProgram;
use App\Models\AssistanceRequest;
use App\Models\MerchantProfile;
use App\Models\User;

trait DemoSeederSupport
{
    protected function demoUser(string $email, string $name, string $role): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => 'password',
                'role' => $role,
                'status' => 'active',
                'email_verified_at' => now()->subDays(10),
            ]
        );
    }

    protected function demoProgram(string $name): AssistanceProgram
    {
        return AssistanceProgram::where('program_name', $name)->firstOrFail();
    }

    protected function demoMember(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    protected function demoMerchant(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    protected function demoMerchantProfile(string $email): MerchantProfile
    {
        return $this->demoMerchant($email)->merchantProfile()->firstOrFail();
    }

    protected function demoRequest(string $referenceCode): AssistanceRequest
    {
        return AssistanceRequest::where('reference_code', $referenceCode)->firstOrFail();
    }

    protected function qrPayload(AssistanceRequest $request): string
    {
        return json_encode([
            'reference_code' => $request->reference_code,
            'request_id' => $request->id,
            'member_id' => $request->member_id,
            'program_id' => $request->program_id,
            'status' => 'Approved',
        ]);
    }

    protected function morphHash(string $seed): string
    {
        return '0x' . substr(hash('sha256', 'edunexus-demo|' . $seed), 0, 64);
    }

    protected function proofHash(array $payload): string
    {
        ksort($payload);

        return hash('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION));
    }

    protected function validationRules(bool $claimed = false): array
    {
        return [
            [
                'key' => 'claim_exists',
                'label' => 'Claim reference verified',
                'passed' => true,
                'message' => 'Valid claim reference found.',
            ],
            [
                'key' => 'claim_approved',
                'label' => 'Approval status confirmed',
                'passed' => true,
                'message' => 'This assistance request is approved for redemption.',
            ],
            [
                'key' => 'not_expired',
                'label' => 'Claim validity active',
                'passed' => true,
                'message' => 'Claim pass is still within its approved redemption period.',
            ],
            [
                'key' => 'merchant_category_allowed',
                'label' => 'Merchant eligibility verified',
                'passed' => true,
                'message' => 'Merchant category matches the assistance program.',
            ],
            [
                'key' => 'not_claimed',
                'label' => 'Redemption availability confirmed',
                'passed' => ! $claimed,
                'message' => $claimed
                    ? 'Already redeemed. This demo claim now has an immutable proof record.'
                    : 'Claim has not been redeemed before.',
            ],
            [
                'key' => 'amount_within_limit',
                'label' => 'Approved amount validated',
                'passed' => true,
                'message' => 'Approved amount is within the request and program limits.',
            ],
        ];
    }
}
