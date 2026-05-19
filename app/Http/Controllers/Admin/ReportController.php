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
                'pending_settlements' => Settlement::where('status', 'Pending')->count(),
                'released_settlements' => Settlement::where('status', 'Settled')->count(),
                'total_reimbursement_value' => Settlement::sum('amount'),
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
                    'pending_amount' => $merchantSettlements->where('status', 'Pending')->sum('amount'),
                    'released_amount' => $merchantSettlements->where('status', 'Settled')->sum('amount'),
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

            return [
                'reference_code' => $transaction->reference_code ?: 'N/A',
                'event_type' => $payload['event_type'] ?? data_get($payload, 'proof_bundle.event_type', $transaction->transaction_type),
                'status' => $transaction->blockchain_status,
                'proof_hash' => $payload['proof_hash'] ?? null,
                'transaction_hash' => $transaction->transaction_hash,
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
