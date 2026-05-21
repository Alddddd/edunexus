<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssistanceRequest;
use App\Models\BlockchainTransaction;
use App\Models\Settlement;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $proofRecords = BlockchainTransaction::query()
            ->latest('recorded_at')
            ->latest('id')
            ->get([
                'id',
                'transaction_type',
                'reference_id',
                'reference_code',
                'transaction_hash',
                'blockchain_status',
                'payload',
                'recorded_at',
                'created_at',
            ]);

        $governanceSummary = $this->governanceSummary($proofRecords);
        $merchantSummaries = $this->merchantSummaries();
        $proofRows = $this->proofRows($proofRecords->take(6));

        if (filled($filters['search'] ?? null)) {
            $search = str($filters['search'])->lower()->toString();

            $merchantSummaries = $merchantSummaries
                ->filter(fn ($summary) => str_contains(strtolower($summary->merchant_name . ' ' . $summary->latest_status), $search))
                ->values();

            $governanceSummary['latest'] = $governanceSummary['latest']
                ->filter(fn (array $row) => str_contains(strtolower(implode(' ', [
                    $row['reference_code'],
                    $row['event_type'],
                    $row['status'],
                    $row['proof_hash'],
                ])), $search))
                ->values();

            $proofRows = $proofRows
                ->filter(fn (array $row) => str_contains(strtolower(implode(' ', [
                    $row['reference_code'],
                    $row['event_type'],
                    $row['status'],
                    $row['proof_hash'],
                    $row['transaction_hash'],
                    $row['settlement_reference'],
                    $row['payout_channel'],
                    $row['settlement_rail'],
                    $row['network'],
                    $row['edux_transfer_status'],
                    $row['edux_transaction_hash'],
                ])), $search))
                ->values();
        }

        return view('admin.reports.index', [
            'filters' => $filters,
            'metrics' => [
                'total_requests' => AssistanceRequest::count(),
                'approved_requests' => AssistanceRequest::where('status', 'Approved')->count(),
                'rejected_requests' => AssistanceRequest::where('status', 'Rejected')->count(),
                'claimed_requests' => AssistanceRequest::where('is_claimed', true)->count(),
                'pending_settlements' => Settlement::whereIn('status', ['Pending', 'Partially Released'])->count(),
                'released_settlements' => Settlement::whereIn('status', ['Released', 'Settled'])->count(),
                'total_reimbursement_value' => Settlement::sum('amount'),
                'settlement_rail_records' => $proofRecords->where('transaction_type', 'Settlement')->count(),
                'total_proof_records' => $proofRecords->count(),
                'successful_proof_records' => $proofRecords->where('blockchain_status', 'Confirmed')->count(),
                'governance_passed' => $governanceSummary['passed'],
                'governance_attention' => $governanceSummary['attention'],
            ],
            'merchantSummaries' => $merchantSummaries,
            'governanceSummary' => $governanceSummary,
            'proofSummary' => [
                'status_counts' => $proofRecords->groupBy('blockchain_status')->map->count(),
                'latest_recorded_at' => $proofRecords->firstWhere('recorded_at')?->recorded_at,
                'event_types' => $proofRecords->pluck('transaction_type')->filter()->unique()->values(),
                'latest_records' => $proofRows,
            ],
        ]);
    }

    public function exportSettlements()
    {
        $filename = 'edunexus-settlement-reimbursement-summary-' . now()->format('Y-m-d') . '.csv';

        return Response::streamDownload(function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'merchant',
                'settlement_count',
                'pending_amount',
                'released_amount',
                'latest_status',
            ]);

            foreach ($this->merchantSummaries() as $summary) {
                fputcsv($handle, [
                    $summary->merchant_name,
                    $summary->settlements_count,
                    number_format((float) $summary->pending_amount, 2, '.', ''),
                    number_format((float) $summary->released_amount, 2, '.', ''),
                    $summary->latest_status,
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function exportProofs()
    {
        $filename = 'edunexus-morph-proof-verification-summary-' . now()->format('Y-m-d') . '.csv';

        return Response::streamDownload(function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'reference_code',
                'event_type',
                'blockchain_status',
                'proof_hash',
                'transaction_hash',
                'settlement_reference',
                'payout_channel',
                'settlement_rail',
                'network',
                'edux_transfer_status',
                'edux_transaction_hash',
                'edux_token_symbol',
                'edux_amount',
                'edux_recipient_wallet',
                'edux_token_contract',
                'recorded_at',
            ]);

            BlockchainTransaction::query()
                ->latest('recorded_at')
                ->latest('id')
                ->chunk(200, function (EloquentCollection $transactions) use ($handle) {
                    foreach ($transactions as $transaction) {
                        $payload = $this->payload($transaction);

                        fputcsv($handle, [
                            $transaction->reference_code ?: 'N/A',
                            $payload['event_type'] ?? data_get($payload, 'proof_bundle.event_type', $transaction->transaction_type),
                            $transaction->blockchain_status,
                            $payload['proof_hash'] ?? '',
                            $transaction->transaction_hash ?? '',
                            $payload['settlement_reference'] ?? data_get($payload, 'proof_bundle.settlement_reference', ''),
                            $payload['payout_channel'] ?? data_get($payload, 'proof_bundle.payout_channel', ''),
                            $payload['settlement_rail'] ?? data_get($payload, 'proof_bundle.settlement_rail', ''),
                            $payload['network'] ?? data_get($payload, 'proof_bundle.network', ''),
                            data_get($payload, 'edux_transfer.edux_transfer_status', data_get($payload, 'proof_bundle.edux_transfer.edux_transfer_status', '')),
                            data_get($payload, 'edux_transfer.edux_transaction_hash', data_get($payload, 'proof_bundle.edux_transfer.edux_transaction_hash', '')),
                            data_get($payload, 'edux_transfer.edux_token_symbol', data_get($payload, 'proof_bundle.edux_transfer.edux_token_symbol', '')),
                            data_get($payload, 'edux_transfer.edux_amount', data_get($payload, 'proof_bundle.edux_transfer.edux_amount', '')),
                            data_get($payload, 'edux_transfer.edux_to', data_get($payload, 'proof_bundle.edux_transfer.edux_to', '')),
                            data_get($payload, 'edux_transfer.edux_token_contract', data_get($payload, 'proof_bundle.edux_transfer.edux_token_contract', '')),
                            optional($transaction->recorded_at)->format('Y-m-d H:i:s') ?: '',
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function merchantSummaries(): Collection
    {
        $settlements = Settlement::with('merchant.merchantProfile')
            ->latest()
            ->get()
            ->groupBy('merchant_id');

        return $settlements
            ->map(function (Collection $merchantSettlements) {
                $latest = $merchantSettlements->sortByDesc('created_at')->first();
                $merchant = $latest?->merchant;

                return (object) [
                    'merchant_name' => $merchant?->merchantProfile?->business_name ?? $merchant?->name ?? 'Unassigned merchant',
                    'settlements_count' => $merchantSettlements->count(),
                    'pending_amount' => $merchantSettlements->whereIn('status', ['Pending', 'Partially Released'])->sum('remaining_balance'),
                    'released_amount' => $merchantSettlements->sum('total_released'),
                    'latest_status' => $latest?->status ?? 'No status',
                ];
            })
            ->sortBy('merchant_name')
            ->values();
    }

    private function governanceSummary(Collection $proofRecords): array
    {
        $rows = $proofRecords
            ->map(function (BlockchainTransaction $transaction) {
                $payload = $this->payload($transaction);
                $rules = collect($payload['validation_rules'] ?? data_get($payload, 'proof_bundle.validation_rules', []));
                $passed = $rules->filter(fn ($rule) => $this->rulePassed((array) $rule))->count();
                $failed = $rules->filter(fn ($rule) => $this->ruleNeedsAttention((array) $rule))->count();

                return [
                    'reference_code' => $transaction->reference_code ?: 'N/A',
                    'event_type' => $payload['event_type'] ?? data_get($payload, 'proof_bundle.event_type', $transaction->transaction_type),
                    'proof_hash' => $payload['proof_hash'] ?? null,
                    'status' => $transaction->blockchain_status,
                    'passed' => $passed,
                    'failed' => $failed,
                    'total' => $rules->count(),
                    'recorded_at' => $transaction->recorded_at,
                ];
            })
            ->filter(fn (array $row) => $row['total'] > 0 || filled($row['proof_hash']))
            ->values();

        return [
            'passed' => $rows->sum('passed'),
            'attention' => $rows->sum('failed'),
            'latest' => $rows->take(6),
        ];
    }

    private function proofRows(Collection $proofRecords): Collection
    {
        return $proofRecords->map(function (BlockchainTransaction $transaction) {
            $payload = $this->payload($transaction);
            $eduxTransfer = $payload['edux_transfer'] ?? data_get($payload, 'proof_bundle.edux_transfer', []);

            return [
                'reference_code' => $transaction->reference_code ?: 'N/A',
                'event_type' => $payload['event_type'] ?? data_get($payload, 'proof_bundle.event_type', $transaction->transaction_type),
                'status' => $transaction->blockchain_status,
                'proof_hash' => $payload['proof_hash'] ?? null,
                'transaction_hash' => $transaction->transaction_hash,
                'settlement_reference' => $payload['settlement_reference'] ?? data_get($payload, 'proof_bundle.settlement_reference'),
                'payout_channel' => $payload['payout_channel'] ?? data_get($payload, 'proof_bundle.payout_channel'),
                'settlement_rail' => $payload['settlement_rail'] ?? data_get($payload, 'proof_bundle.settlement_rail'),
                'network' => $payload['network'] ?? data_get($payload, 'proof_bundle.network'),
                'edux_transfer_status' => $eduxTransfer['edux_transfer_status'] ?? null,
                'edux_transaction_hash' => $eduxTransfer['edux_transaction_hash'] ?? null,
                'edux_token_symbol' => $eduxTransfer['edux_token_symbol'] ?? 'EDUX',
                'edux_amount' => $eduxTransfer['edux_amount'] ?? null,
                'edux_to' => $eduxTransfer['edux_to'] ?? null,
                'edux_token_contract' => $eduxTransfer['edux_token_contract'] ?? null,
                'recorded_at' => $transaction->recorded_at,
            ];
        });
    }

    private function payload(BlockchainTransaction $transaction): array
    {
        return json_decode($transaction->payload ?: '[]', true) ?: [];
    }

    private function rulePassed(array $rule): bool
    {
        $value = strtolower((string) ($rule['status'] ?? $rule['result'] ?? $rule['outcome'] ?? ''));

        return ($rule['passed'] ?? false) === true || in_array($value, ['passed', 'pass', 'success', 'valid'], true);
    }

    private function ruleNeedsAttention(array $rule): bool
    {
        $value = strtolower((string) ($rule['status'] ?? $rule['result'] ?? $rule['outcome'] ?? ''));

        return ($rule['passed'] ?? null) === false || in_array($value, ['failed', 'fail', 'rejected', 'invalid', 'attention'], true);
    }
}
