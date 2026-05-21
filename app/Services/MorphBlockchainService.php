<?php

namespace App\Services;

use Symfony\Component\Process\Process;

class MorphBlockchainService
{
    public function transferSettlementToken(): array
    {
        $enabled = filter_var(env('EDUX_DEMO_TRANSFER_ENABLED', false), FILTER_VALIDATE_BOOLEAN);

        if (! $enabled) {
            return [
                'success' => true,
                'edux_transfer_enabled' => false,
                'edux_transfer_status' => 'skipped',
                'transaction_hash' => null,
                'receipt_status' => 'skipped',
                'from_address' => null,
                'to_address' => env('EDUX_SETTLEMENT_RECIPIENT_WALLET'),
                'token_symbol' => 'EDUX',
                'token_amount' => env('EDUX_DEMO_TRANSFER_AMOUNT', '1'),
                'token_contract' => env('EDUX_TOKEN_ADDRESS'),
                'block_number' => null,
                'edux_error' => 'EDUX demo transfer is disabled or not configured.',
            ];
        }

        $required = [
            'MORPH_RPC_URL',
            'MORPH_PRIVATE_KEY',
            'EDUX_TOKEN_ADDRESS',
            'EDUX_SETTLEMENT_RECIPIENT_WALLET',
        ];

        $missing = array_values(array_filter($required, fn (string $key) => blank(env($key))));

        if ($missing !== []) {
            return [
                'success' => true,
                'edux_transfer_enabled' => true,
                'edux_transfer_status' => 'skipped',
                'transaction_hash' => null,
                'receipt_status' => 'skipped',
                'from_address' => null,
                'to_address' => env('EDUX_SETTLEMENT_RECIPIENT_WALLET'),
                'token_symbol' => 'EDUX',
                'token_amount' => env('EDUX_DEMO_TRANSFER_AMOUNT', '1'),
                'token_contract' => env('EDUX_TOKEN_ADDRESS'),
                'block_number' => null,
                'edux_error' => 'Missing EDUX transfer configuration: ' . implode(', ', $missing),
            ];
        }

        $process = new Process([
            'node',
            base_path('scripts/transfer-settlement-token.js'),
        ]);

        $process->setWorkingDirectory(base_path());
        $process->setTimeout(120);
        $process->run();

        $output = json_decode(trim($process->getOutput()), true) ?: [];

        if (! $process->isSuccessful()) {
            return [
                'success' => false,
                'edux_transfer_enabled' => true,
                'edux_transfer_status' => 'failed',
                'edux_transaction_hash' => $output['transaction_hash'] ?? null,
                'transaction_hash' => $output['transaction_hash'] ?? null,
                'receipt_status' => 'failed',
                'from_address' => $output['from_address'] ?? null,
                'to_address' => $output['to_address'] ?? env('EDUX_SETTLEMENT_RECIPIENT_WALLET'),
                'token_symbol' => $output['token_symbol'] ?? 'EDUX',
                'token_amount' => $output['token_amount'] ?? env('EDUX_DEMO_TRANSFER_AMOUNT', '1'),
                'token_contract' => $output['token_contract'] ?? env('EDUX_TOKEN_ADDRESS'),
                'block_number' => $output['block_number'] ?? null,
                'edux_error' => $output['error'] ?? ($process->getErrorOutput() ?: $process->getOutput()),
            ];
        }

        return [
            'success' => (bool) ($output['success'] ?? false),
            'edux_transfer_enabled' => true,
            'edux_transfer_status' => ($output['receipt_status'] ?? null) === 'success' ? 'success' : 'failed',
            'edux_transaction_hash' => $output['transaction_hash'] ?? null,
            'edux_from' => $output['from_address'] ?? null,
            'edux_to' => $output['to_address'] ?? null,
            'edux_amount' => $output['token_amount'] ?? env('EDUX_DEMO_TRANSFER_AMOUNT', '1'),
            'edux_token_symbol' => $output['token_symbol'] ?? 'EDUX',
            'edux_token_contract' => $output['token_contract'] ?? env('EDUX_TOKEN_ADDRESS'),
            'edux_block_number' => $output['block_number'] ?? null,
            'transaction_hash' => $output['transaction_hash'] ?? null,
            'receipt_status' => $output['receipt_status'] ?? null,
            'from_address' => $output['from_address'] ?? null,
            'to_address' => $output['to_address'] ?? null,
            'token_symbol' => $output['token_symbol'] ?? 'EDUX',
            'token_amount' => $output['token_amount'] ?? env('EDUX_DEMO_TRANSFER_AMOUNT', '1'),
            'token_contract' => $output['token_contract'] ?? env('EDUX_TOKEN_ADDRESS'),
            'block_number' => $output['block_number'] ?? null,
            'edux_error' => $output['error'] ?? null,
        ];
    }

    public function recordClaimProof(string $referenceCode, float $amount, int $merchantId): array
    {
        return $this->recordProof($referenceCode, $amount, $merchantId);
    }

    public function recordSettlementProof(string $settlementReference, float $amount, int $merchantId): array
    {
        return $this->recordProof($settlementReference, $amount, $merchantId);
    }

    private function recordProof(string $referenceCode, float $amount, int $merchantId): array
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
