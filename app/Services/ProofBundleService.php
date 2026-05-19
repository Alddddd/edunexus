<?php

namespace App\Services;

use App\Models\AssistanceRequest;
use App\Models\MerchantProfile;

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
