<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use kornrunner\Ethereum\Transaction;
use kornrunner\Keccak;
use kornrunner\Serializer\HexPrivateKeySerializer;
use Mdanter\Ecc\EccFactory;
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
        $rpcUrl = env('MORPH_RPC_URL');
        $privateKey = $this->normalizePrivateKey(env('MORPH_PRIVATE_KEY'));
        $contractAddress = env('MORPH_CONTRACT_ADDRESS');
        $rpcConfigured = filled($rpcUrl);
        $privateKeyConfigured = filled($privateKey);
        $contractConfigured = filled($contractAddress);

        Log::info('Morph proof recording starting.', [
            'rpc_configured' => $rpcConfigured,
            'private_key_configured' => $privateKeyConfigured,
            'contract_configured' => $contractConfigured,
            'runtime' => 'php-json-rpc',
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

        if (! $this->isValidPrivateKey($privateKey)) {
            Log::warning('Morph proof recording private key is invalid.');

            return [
                'success' => false,
                'transaction_hash' => null,
                'error' => 'MORPH_PRIVATE_KEY is not a valid EVM private key.',
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

        try {
            $result = $this->sendProofTransaction(
                (string) $rpcUrl,
                (string) $privateKey,
                (string) $contractAddress,
                $referenceCode,
                $amount,
                $merchantId
            );

            $transactionHash = $result['transaction_hash'] ?? null;
            $error = $result['error'] ?? null;

            Log::info('Morph proof recording finished.', [
                'runtime' => 'php-json-rpc',
                'receipt_status' => $result['receipt_status'] ?? null,
                'block_number' => $result['block_number'] ?? null,
                'tx_hash_detected' => $this->isValidTransactionHash($transactionHash),
                'error_summary' => $this->summarizeOutput((string) $error),
            ]);

            return [
                'success' => (bool) ($result['success'] ?? false) && $this->isValidTransactionHash($transactionHash),
                'transaction_hash' => $this->isValidTransactionHash($transactionHash) ? $transactionHash : null,
                'error' => $error,
            ];
        } catch (\Throwable $exception) {
            Log::error('Morph proof recording failed before transaction result.', [
                'runtime' => 'php-json-rpc',
                'tx_hash_detected' => false,
                'error_summary' => $this->summarizeOutput($exception->getMessage()),
            ]);

            return [
                'success' => false,
                'transaction_hash' => null,
                'error' => $exception->getMessage(),
            ];
        }
    }

    private function sendProofTransaction(
        string $rpcUrl,
        string $privateKey,
        string $contractAddress,
        string $referenceCode,
        float $amount,
        int $merchantId
    ): array {
        $fromAddress = $this->addressFromPrivateKey($privateKey);
        $data = '0x' . $this->encodeRecordClaimProofData($referenceCode, $amount, $merchantId);
        $nonce = $this->rpcHex($rpcUrl, 'eth_getTransactionCount', [$fromAddress, 'pending']);
        $chainIdHex = $this->rpcHex($rpcUrl, 'eth_chainId');
        $gasPrice = $this->rpcHex($rpcUrl, 'eth_gasPrice');
        $gasLimit = $this->gasLimit($rpcUrl, $fromAddress, $contractAddress, $data);
        $chainId = $this->hexToInt($chainIdHex);

        $transaction = new Transaction(
            $this->stripHex($nonce),
            $this->stripHex($gasPrice),
            $this->stripHex($gasLimit),
            $this->stripHex($contractAddress),
            '',
            $this->stripHex($data)
        );

        $rawTransaction = '0x' . $transaction->getRaw($privateKey, $chainId);
        $transactionHash = $this->rpc($rpcUrl, 'eth_sendRawTransaction', [$rawTransaction]);

        if (! $this->isValidTransactionHash($transactionHash)) {
            return [
                'success' => false,
                'transaction_hash' => null,
                'receipt_status' => 'failed',
                'block_number' => null,
                'error' => 'Morph RPC did not return a valid transaction hash.',
            ];
        }

        $receipt = $this->waitForReceipt($rpcUrl, $transactionHash);
        $receiptStatus = $receipt['status'] ?? null;
        $success = $receiptStatus === '0x1';

        return [
            'success' => $success,
            'transaction_hash' => $transactionHash,
            'receipt_status' => $success ? 'success' : ($receipt ? 'failed' : 'pending'),
            'block_number' => isset($receipt['blockNumber']) ? $this->hexToInt($receipt['blockNumber']) : null,
            'error' => $success ? null : ($receipt ? 'Morph transaction receipt status was not successful.' : 'Morph transaction receipt was not available before timeout.'),
        ];
    }

    private function rpc(string $rpcUrl, string $method, array $params = []): mixed
    {
        $response = Http::timeout(30)
            ->acceptJson()
            ->post($rpcUrl, [
                'jsonrpc' => '2.0',
                'id' => 1,
                'method' => $method,
                'params' => $params,
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException("Morph RPC {$method} failed with HTTP {$response->status()}.");
        }

        $payload = $response->json();

        if (isset($payload['error'])) {
            $message = is_array($payload['error'])
                ? ($payload['error']['message'] ?? json_encode($payload['error']))
                : (string) $payload['error'];

            throw new \RuntimeException("Morph RPC {$method} error: {$message}");
        }

        return $payload['result'] ?? null;
    }

    private function rpcHex(string $rpcUrl, string $method, array $params = []): string
    {
        $result = $this->rpc($rpcUrl, $method, $params);

        if (! is_string($result) || preg_match('/^0x[a-fA-F0-9]+$/', $result) !== 1) {
            throw new \RuntimeException("Morph RPC {$method} did not return a hex value.");
        }

        return $result;
    }

    private function gasLimit(string $rpcUrl, string $fromAddress, string $contractAddress, string $data): string
    {
        try {
            $estimated = $this->rpcHex($rpcUrl, 'eth_estimateGas', [[
                'from' => $fromAddress,
                'to' => $contractAddress,
                'value' => '0x0',
                'data' => $data,
            ]]);

            return '0x' . dechex(max((int) ceil($this->hexToInt($estimated) * 1.2), 250000));
        } catch (\Throwable $exception) {
            Log::warning('Morph gas estimation failed; using fixed proof gas limit.', [
                'error_summary' => $this->summarizeOutput($exception->getMessage()),
            ]);

            return '0x493e0';
        }
    }

    private function waitForReceipt(string $rpcUrl, string $transactionHash): ?array
    {
        for ($attempt = 0; $attempt < 30; $attempt++) {
            $receipt = $this->rpc($rpcUrl, 'eth_getTransactionReceipt', [$transactionHash]);

            if (is_array($receipt)) {
                return $receipt;
            }

            sleep(2);
        }

        return null;
    }

    private function encodeRecordClaimProofData(string $referenceCode, float $amount, int $merchantId): string
    {
        $selector = substr(Keccak::hash('recordClaimProof(string,uint256,uint256)', 256), 0, 8);
        $amountInCents = (int) round($amount * 100);
        $referenceHex = bin2hex($referenceCode);
        $referenceBytes = intdiv(strlen($referenceHex), 2);

        return $selector
            . $this->uint256Hex(96)
            . $this->uint256Hex($amountInCents)
            . $this->uint256Hex($merchantId)
            . $this->uint256Hex($referenceBytes)
            . str_pad($referenceHex, (int) ceil(max($referenceBytes, 1) / 32) * 64, '0', STR_PAD_RIGHT);
    }

    private function addressFromPrivateKey(string $privateKey): string
    {
        $generator = EccFactory::getSecgCurves()->generator256k1(null, true);
        $serializer = new HexPrivateKeySerializer($generator);
        $publicKey = $serializer->parse($privateKey)->getPublicKey()->getPoint();
        $x = str_pad(gmp_strval($publicKey->getX(), 16), 64, '0', STR_PAD_LEFT);
        $y = str_pad(gmp_strval($publicKey->getY(), 16), 64, '0', STR_PAD_LEFT);

        return '0x' . substr(Keccak::hash(hex2bin($x . $y), 256), -40);
    }

    private function summarizeOutput(string $output): ?string
    {
        if ($output === '') {
            return null;
        }

        return substr(preg_replace('/\s+/', ' ', $output) ?: $output, 0, 500);
    }

    private function normalizePrivateKey(?string $privateKey): ?string
    {
        if (! is_string($privateKey)) {
            return null;
        }

        return $this->stripHex(trim($privateKey));
    }

    private function stripHex(?string $value): string
    {
        $value = trim((string) $value);

        return str_starts_with($value, '0x') || str_starts_with($value, '0X')
            ? substr($value, 2)
            : $value;
    }

    private function uint256Hex(int $value): string
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Unsigned integer ABI values cannot be negative.');
        }

        return str_pad(dechex($value), 64, '0', STR_PAD_LEFT);
    }

    private function hexToInt(?string $value): int
    {
        return (int) hexdec($this->stripHex($value));
    }

    private function isValidPrivateKey(?string $privateKey): bool
    {
        return is_string($privateKey) && preg_match('/^[a-fA-F0-9]{64}$/', $privateKey) === 1;
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
