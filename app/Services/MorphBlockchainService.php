<?php

namespace App\Services;

use Symfony\Component\Process\Process;

class MorphBlockchainService
{
    public function recordClaimProof(string $referenceCode, float $amount, int $merchantId): array
    {
        $process = new Process([
            'node',
            base_path('scripts/record-claim-proof.js'),
            $referenceCode,
            (string) $amount,
            (string) $merchantId,
        ]);

        $process->setWorkingDirectory(base_path());
        $process->setTimeout(60);
        $process->run();

        if (! $process->isSuccessful()) {
            return [
                'success' => false,
                'transaction_hash' => null,
                'error' => $process->getErrorOutput() ?: $process->getOutput(),
            ];
        }

        $output = json_decode($process->getOutput(), true);

        return [
            'success' => $output['success'] ?? false,
            'transaction_hash' => $output['transaction_hash'] ?? null,
            'error' => $output['error'] ?? null,
        ];
    }
}