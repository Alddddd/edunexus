@extends('layouts.dashboard')

@section('title', 'Audit Reports')

@section('content')

@php
    $statusTone = function ($status) {
        return match ($status) {
            'Confirmed', 'Settled' => 'success',
            'Failed', 'Rejected' => 'danger',
            'Pending' => 'warning',
            default => 'neutral',
        };
    };

    $settlementLabel = function ($status) {
        return match ($status) {
            'Pending' => 'Ready for Release',
            'Settled' => 'Released',
            default => $status,
        };
    };

    $truncateHash = fn ($hash) => $hash ? substr($hash, 0, 18) . '...' . substr($hash, -12) : null;
@endphp

<div class="w-full min-w-0 max-w-7xl space-y-6">
    <x-page-header
        title="Institutional Reports"
        eyebrow="Reporting & Audit Infrastructure"
        description="Readable operational summaries for assistance utilization, reimbursements, governance validation, Morph proof verification, and settlement release activity.">
        <x-slot:actions>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.reports.exports.settlements') }}"
                   class="inline-flex min-h-11 items-center justify-center rounded-xl border border-ui-action/20 bg-ui-action/10 px-4 py-2 text-sm font-semibold text-ui-action shadow-sm transition hover:bg-ui-action/15">
                    Export reimbursements CSV
                </a>

                <a href="{{ route('admin.reports.exports.proofs') }}"
                   class="inline-flex min-h-11 items-center justify-center rounded-xl border border-ui-proof/20 bg-ui-proof/10 px-4 py-2 text-sm font-semibold text-ui-proof shadow-sm transition hover:bg-ui-proof/15">
                    Export proofs CSV
                </a>
            </div>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
        <section class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-ui-subtext">Assistance Utilization</p>
                    <p class="mt-2 text-2xl font-bold text-ui-text">{{ number_format($metrics['total_requests']) }}</p>
                </div>
                <span class="rounded-xl bg-ui-action/10 p-2 text-ui-action">
                    <x-icon name="lifebuoy" size="h-5 w-5" />
                </span>
            </div>
            <p class="mt-3 text-xs text-ui-subtext">
                {{ number_format($metrics['approved_requests']) }} approved,
                {{ number_format($metrics['rejected_requests']) }} rejected,
                {{ number_format($metrics['claimed_requests']) }} claimed
            </p>
        </section>

        <section class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-ui-subtext">Merchant Reimbursements</p>
                    <p class="mt-2 text-2xl font-bold text-ui-text">&#8369;{{ number_format($metrics['total_reimbursement_value'], 2) }}</p>
                </div>
                <span class="rounded-xl bg-emerald-50 p-2 text-ui-success">
                    <x-icon name="credit-card" size="h-5 w-5" />
                </span>
            </div>
            <p class="mt-3 text-xs text-ui-subtext">
                {{ number_format($metrics['pending_settlements']) }} pending,
                {{ number_format($metrics['released_settlements']) }} released
            </p>
        </section>

        <section class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-ui-subtext">Governance Validation</p>
                    <p class="mt-2 text-2xl font-bold text-ui-success">{{ number_format($metrics['governance_passed']) }}</p>
                </div>
                <span class="rounded-xl bg-cyan-50 p-2 text-ui-proof">
                    <x-icon name="shield-check" size="h-5 w-5" />
                </span>
            </div>
            <p class="mt-3 text-xs text-ui-subtext">
                {{ number_format($metrics['governance_attention']) }} checks need attention
            </p>
        </section>

        <section class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-ui-subtext">Morph Proof Records</p>
                    <p class="mt-2 text-2xl font-bold text-ui-proof">{{ number_format($metrics['total_proof_records']) }}</p>
                </div>
                <span class="rounded-xl bg-cyan-50 p-2 text-ui-proof">
                    <x-icon name="link" size="h-5 w-5" />
                </span>
            </div>
            <p class="mt-3 text-xs text-ui-subtext">
                {{ number_format($metrics['successful_proof_records']) }} confirmed proof records
            </p>
        </section>

        <section class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm text-ui-subtext">Settlement Lifecycle</p>
                    <p class="mt-2 text-2xl font-bold text-ui-warning">{{ number_format($metrics['pending_settlements']) }}</p>
                </div>
                <span class="rounded-xl bg-amber-50 p-2 text-ui-warning">
                    <x-icon name="list-checks" size="h-5 w-5" />
                </span>
            </div>
            <p class="mt-3 text-xs text-ui-subtext">
                Pending release review queue
            </p>
        </section>
    </div>

    <section class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-emerald-800">
                    Audit Presentation Layer
                </p>

                <p class="mt-1 max-w-3xl text-sm leading-6 text-emerald-700">
                    These reports summarize existing operational records only. They do not alter assistance approvals, merchant claim processing, settlement releases, or Morph proof logging.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <x-status-badge status="Read-only reports" tone="success" />
                <x-status-badge status="Export-ready" tone="proof" />
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60 sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-base font-semibold text-ui-text">
                    Search Audit Reports
                </h2>

                <p class="mt-1 text-sm leading-6 text-ui-subtext">
                    Filter visible summaries by merchant, reference code, proof hash, transaction hash, event type, or status.
                </p>
            </div>

            @if(filled($filters['search'] ?? null))
                <a href="{{ route('admin.reports.index') }}"
                   class="inline-flex min-h-10 items-center justify-center rounded-xl border border-ui-border px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-ui-canvas">
                    Clear search
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('admin.reports.index') }}" class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-[1fr_auto]">
            <div>
                <label for="search" class="block text-sm font-semibold text-slate-700">
                    Search
                </label>

                <input id="search"
                       name="search"
                       type="search"
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Reference, merchant, hash, status, or event"
                       class="mt-2 w-full rounded-xl border-slate-200 text-sm text-slate-700 shadow-sm focus:border-teal-500 focus:ring-teal-500">
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-ui-action/20 bg-ui-action/10 px-5 py-2.5 text-sm font-semibold text-ui-action shadow-sm transition hover:bg-ui-action/15 lg:w-auto">
                    Search Reports
                </button>
            </div>
        </form>
    </section>

    <x-table-card
        title="Merchant Reimbursement Summary"
        description="Grouped settlement value by merchant for cooperative reimbursement review.">
        <x-slot:actions>
            <a href="{{ route('admin.reports.exports.settlements') }}"
               class="inline-flex min-h-10 items-center justify-center rounded-xl border border-ui-border px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-ui-canvas">
                Download CSV
            </a>
        </x-slot:actions>

        <table class="min-w-full divide-y divide-ui-border text-sm">
            <thead class="bg-ui-canvas/70">
                <tr>
                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Merchant</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Settlements</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Pending Amount</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Released Amount</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Latest Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ui-border bg-white">
                @forelse($merchantSummaries as $summary)
                    <tr>
                        <td class="px-5 py-4 font-semibold text-ui-text">{{ $summary->merchant_name }}</td>
                        <td class="px-5 py-4 text-ui-subtext">{{ number_format($summary->settlements_count) }}</td>
                        <td class="px-5 py-4 font-semibold text-ui-text">&#8369;{{ number_format($summary->pending_amount, 2) }}</td>
                        <td class="px-5 py-4 font-semibold text-ui-text">&#8369;{{ number_format($summary->released_amount, 2) }}</td>
                        <td class="px-5 py-4">
                            <x-status-badge :status="$settlementLabel($summary->latest_status)" :tone="$statusTone($summary->latest_status)" />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <p class="font-semibold text-ui-text">No reimbursement records yet</p>
                            <p class="mt-2 text-sm text-ui-subtext">Merchant settlement summaries will appear after valid claims are processed.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <x-table-card
            title="Governance Validation Summary"
            description="Latest proof bundles with readable governance check outcomes.">
            <table class="min-w-full divide-y divide-ui-border text-sm">
                <thead class="bg-ui-canvas/70">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Reference</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Checks</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Proof Hash</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ui-border bg-white">
                    @forelse($governanceSummary['latest'] as $row)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-ui-text">{{ $row['reference_code'] }}</p>
                                <p class="mt-1 text-xs text-ui-subtext">{{ $row['event_type'] }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <x-status-badge status="{{ $row['passed'] }} passed" tone="success" size="xs" />
                                    @if($row['failed'] > 0)
                                        <x-status-badge status="{{ $row['failed'] }} attention" tone="danger" size="xs" />
                                    @else
                                        <x-status-badge status="No exceptions" tone="proof" size="xs" />
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                @if($row['proof_hash'])
                                    <p class="max-w-xs break-all font-mono text-xs text-cyan-800" title="{{ $row['proof_hash'] }}">
                                        {{ $truncateHash($row['proof_hash']) }}
                                    </p>
                                @else
                                    <span class="text-sm text-ui-subtext">No proof hash stored</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-12 text-center">
                                <p class="font-semibold text-ui-text">No governance validation payloads yet</p>
                                <p class="mt-2 text-sm text-ui-subtext">Structured validation summaries will appear when proof bundles include rule results.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-table-card>

        <x-table-card
            title="Morph Proof Verification Summary"
            description="Latest proof records and status counts for auditor review.">
            <x-slot:actions>
                <a href="{{ route('admin.reports.exports.proofs') }}"
                   class="inline-flex min-h-10 items-center justify-center rounded-xl border border-ui-border px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-ui-canvas">
                    Download CSV
                </a>
            </x-slot:actions>

            <div class="border-b border-ui-border bg-ui-canvas/50 px-5 py-4">
                <div class="flex flex-wrap gap-2">
                    @forelse($proofSummary['status_counts'] as $status => $count)
                        <x-status-badge status="{{ $status }}: {{ number_format($count) }}" :tone="$statusTone($status)" size="xs" />
                    @empty
                        <x-status-badge status="No proof records" tone="neutral" size="xs" />
                    @endforelse
                </div>

                <p class="mt-3 text-xs text-ui-subtext">
                    Latest recorded timestamp:
                    <span class="font-semibold text-ui-text">
                        {{ $proofSummary['latest_recorded_at']?->format('M d, Y h:i A') ?? 'No timestamp recorded' }}
                    </span>
                </p>
            </div>

            <table class="min-w-full divide-y divide-ui-border text-sm">
                <thead class="bg-ui-canvas/70">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Proof Record</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Status</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Hash</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ui-border bg-white">
                    @forelse($proofSummary['latest_records'] as $record)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-ui-text">{{ $record['reference_code'] }}</p>
                                <p class="mt-1 text-xs text-ui-subtext">{{ $record['event_type'] }}</p>
                                <p class="mt-1 text-xs text-ui-subtext">{{ $record['recorded_at']?->format('M d, Y h:i A') ?? 'No timestamp' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <x-status-badge :status="$record['status']" :tone="$statusTone($record['status'])" />
                            </td>
                            <td class="px-5 py-4">
                                @if($record['proof_hash'])
                                    <p class="max-w-xs break-all font-mono text-xs text-cyan-800" title="{{ $record['proof_hash'] }}">
                                        {{ $truncateHash($record['proof_hash']) }}
                                    </p>
                                @elseif($record['transaction_hash'])
                                    <p class="max-w-xs break-all font-mono text-xs text-cyan-800" title="{{ $record['transaction_hash'] }}">
                                        {{ $truncateHash($record['transaction_hash']) }}
                                    </p>
                                @else
                                    <span class="text-sm text-ui-subtext">Hash pending</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-12 text-center">
                                <p class="font-semibold text-ui-text">No Morph proof records yet</p>
                                <p class="mt-2 text-sm text-ui-subtext">Proof verification records will appear after merchant claim processing records proof.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-table-card>
    </div>
</div>

@endsection
