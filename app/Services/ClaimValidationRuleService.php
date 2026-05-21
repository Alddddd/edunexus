<?php

namespace App\Services;

use App\Models\AssistanceRequest;
use App\Models\MerchantProfile;

class ClaimValidationRuleService
{
    public function evaluate(AssistanceRequest $assistanceRequest, ?MerchantProfile $merchantProfile): array
    {
        $assistanceRequest->loadMissing('program');

        $program = $assistanceRequest->program;
        $merchantCategoryAllowed = false;

        if ($merchantProfile && $merchantProfile->status === 'Active' && $program) {
            $merchantCategoryAllowed = $merchantProfile->merchant_category_id && $program->merchant_category_id
                ? (int) $merchantProfile->merchant_category_id === (int) $program->merchant_category_id
                : strtolower((string) $merchantProfile->merchant_category) === strtolower((string) $program->merchant_category);
        }

        $amountWithinLimit =
            $assistanceRequest->approved_amount !== null &&
            (float) $assistanceRequest->approved_amount > 0 &&
            (float) $assistanceRequest->approved_amount <= (float) $assistanceRequest->requested_amount &&
            (float) $assistanceRequest->approved_amount <= (float) $program->maximum_amount;

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
                'passed' => $assistanceRequest->status === 'Approved',
                'message' => $assistanceRequest->status === 'Approved'
                    ? 'This assistance request is approved for redemption.'
                    : 'This assistance request is not approved for merchant validation.',
            ],
            [
                'key' => 'not_expired',
                'label' => 'Claim validity active',
                'passed' => $assistanceRequest->expiration_date && now()->lessThanOrEqualTo($assistanceRequest->expiration_date),
                'message' => $assistanceRequest->expiration_date && now()->lessThanOrEqualTo($assistanceRequest->expiration_date)
                    ? 'Claim pass is still within its approved redemption period.'
                    : 'Claim validity expired. This claim pass is outside its approved redemption period.',
            ],
            [
                'key' => 'merchant_category_allowed',
                'label' => 'Merchant eligibility verified',
                'passed' => $merchantCategoryAllowed,
                'message' => $merchantCategoryAllowed
                    ? 'Merchant category matches the assistance program.'
                    : 'Merchant category mismatch. This merchant is not accredited for the assistance program category.',
            ],
            [
                'key' => 'not_claimed',
                'label' => 'Redemption availability confirmed',
                'passed' => ! $assistanceRequest->is_claimed,
                'message' => ! $assistanceRequest->is_claimed
                    ? 'Claim has not been redeemed before.'
                    : 'Already redeemed. This claim pass cannot be processed again.',
            ],
            [
                'key' => 'amount_within_limit',
                'label' => 'Approved amount validated',
                'passed' => $amountWithinLimit,
                'message' => $amountWithinLimit
                    ? 'Approved amount is within the request and program limits.'
                    : 'Approved amount is missing or outside the request and program limits.',
            ],
        ];
    }

    public function allPassed(array $rules): bool
    {
        return collect($rules)->every(fn (array $rule) => (bool) $rule['passed']);
    }

    public function firstFailureMessage(array $rules): ?string
    {
        $failedRule = collect($rules)->first(fn (array $rule) => ! $rule['passed']);

        return $failedRule['message'] ?? null;
    }

    public function summary(array $rules): array
    {
        return collect($rules)
            ->map(fn (array $rule) => [
                'key' => $rule['key'],
                'label' => $rule['label'],
                'passed' => (bool) $rule['passed'],
                'message' => $rule['message'],
            ])
            ->values()
            ->all();
    }
}
