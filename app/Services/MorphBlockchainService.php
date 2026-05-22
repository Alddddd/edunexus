<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
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
        $transactionHash = $output['transaction_hash'] ?? null;

        if (! $process->isSuccessful()) {
            return [
                'success' => false,
                'edux_transfer_enabled' => true,
                'edux_transfer_status' => 'failed',
                'edux_transaction_hash' => $transactionHash,
                'transaction_hash' => $transactionHash,
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

        $eduxSuccess = (bool) ($output['success'] ?? false) && $this->isValidTransactionHash($output['transaction_hash'] ?? null);

        return [
            'success' => $eduxSuccess,
            'edux_transfer_enabled' => true,
            'edux_transfer_status' => $eduxSuccess ? 'success' : 'failed',
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
        $scriptPath = base_path('scripts/record-claim-proof.js');
        $rpcConfigured = filled(env('MORPH_RPC_URL'));
        $privateKeyConfigured = filled(env('MORPH_PRIVATE_KEY'));
        $contractAddress = env('MORPH_CONTRACT_ADDRESS');
        $contractConfigured = filled($contractAddress);

        Log::info('Morph proof recording starting.', [
            'rpc_configured' => $rpcConfigured,
            'private_key_configured' => $privateKeyConfigured,
            'contract_configured' => $contractConfigured,
            'script_path' => $scriptPath,
        ]);

        if (! $rpcConfigured || ! $privateKeyConfigured || ! $contractConfigured) {
            Log::warning('Morph proof recording configuration missing.', [
                'rpc_configured' => $rpcConfigured,
                'private_key_configured' => $privateKeyConfigured,
                'contract_configured' => $contractConfigured,
            ]);

            return [
                'success' => false,
                'transaction_hash' => null,
                'error' => 'Missing Morph blockchain configuration.',
            ];
        }

        if (! $this->isValidAddress($contractAddress)) {
            Log::warning('Morph proof recording contract address is invalid.', [
                'contract_configured' => true,
            ]);

            return [
                'success' => false,
                'transaction_hash' => null,
                'error' => 'MORPH_CONTRACT_ADDRESS is not a valid EVM address.',
            ];
        }

        if (! is_file($scriptPath)) {
            Log::warning('Morph proof recording script was not found.', [
                'script_path' => $scriptPath,
            ]);

            return [
                'success' => false,
                'transaction_hash' => null,
                'error' => 'Morph proof script was not found.',
            ];
        }

        $process = new Process([
            'node',
            $scriptPath,
            $referenceCode,
            (string) $amount,
            (string) $merchantId,
        ]);

        $process->setWorkingDirectory(base_path());
        $process->setTimeout(60);
        $process->run();

        $stdout = trim($process->getOutput());
        $stderr = trim($process->getErrorOutput());
        $output = $this->decodeNodeJson($stdout);
        $transactionHash = $this->transactionHashFromOutput($output);

        Log::info('Morph proof recording finished.', [
            'script_path' => $scriptPath,
            'process_successful' => $process->isSuccessful(),
            'stdout_summary' => $this->summarizeOutput($stdout),
            'stderr_summary' => $this->summarizeOutput($stderr),
            'tx_hash_detected' => $this->isValidTransactionHash($transactionHash),
            'exit_code' => $process->getExitCode(),
        ]);

        if (! $process->isSuccessful()) {
            return [
                'success' => false,
                'transaction_hash' => $this->isValidTransactionHash($transactionHash) ? $transactionHash : null,
                'error' => $output['error'] ?? ($stderr ?: $stdout),
            ];
        }

        return [
            'success' => (bool) ($output['success'] ?? false) && $this->isValidTransactionHash($transactionHash),
            'transaction_hash' => $transactionHash,
            'error' => $output['error'] ?? ($transactionHash ? null : 'Morph transaction hash was not returned.'),
        ];
    }

    private function decodeNodeJson(string $stdout): array
    {
        $decoded = json_decode($stdout, true);

        if (is_array($decoded)) {
            return $decoded;
        }

        foreach (array_reverse(preg_split('/\R/', $stdout) ?: []) as $line) {
            $decoded = json_decode(trim($line), true);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    private function transactionHashFromOutput(array $output): ?string
    {
        foreach (['transaction_hash', 'transactionHash', 'hash'] as $key) {
            if (isset($output[$key]) && $this->isValidTransactionHash($output[$key])) {
                return $output[$key];
            }
        }

        return null;
    }

    private function summarizeOutput(string $output): ?string
    {
        if ($output === '') {
            return null;
        }

        return substr(preg_replace('/\s+/', ' ', $output) ?: $output, 0, 500);
    }

    private function isValidAddress(?string $address): bool
    {
        return is_string($address) && preg_match('/^0x[a-fA-F0-9]{40}$/', $address) === 1;
    }

    private function isValidTransactionHash(?string $hash): bool
    {
        return is_string($hash) && preg_match('/^0x[a-fA-F0-9]{64}$/', $hash) === 1;
    }
}
