@extends('layouts.dashboard')

@section('title', 'Morph Verification Console')

@section('content')

@php
    $hasFilters = filled($filters['blockchain_status'] ?? null) || filled($filters['transaction_type'] ?? null);
    $explorerBaseUrl = 'https://explorer-hoodi.morph.network/tx/';

    $statusTone = function ($status) {
        return match ($status) {
            'Confirmed' => 'success',
            'Failed' => 'danger',
            default => 'warning',
        };
    };

    $typeTone = function ($type) {
        return match ($type) {
            'Approval' => 'success',
            'Settlement' => 'proof',
            default => 'neutral',
        };
    };

    $typeIcons = function ($type) {
        return match ($type) {
            'Approval' => 'check-circle',
            'Settlement' => 'credit-card',
            default => 'link',
        };
    };
@endphp

<div class="max-w-7xl space-y-6">
    <x-page-header
        title="Morph Verification Console"
        eyebrow="Morph Proof Layer"
        description="Monitor Morph blockchain proof records generated from cooperative assistance validation and settlement workflows.">
        <x-slot:actions>
            <div class="rounded-2xl border border-cyan-100 bg-cyan-50 px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-cyan-700">
                    Current View
                </p>

                <p class="mt-1 text-2xl font-bold text-cyan-800">
                    {{ number_format($transactions->total()) }}
                </p>

                <p class="text-xs text-cyan-700">
                    {{ $hasFilters ? 'Filtered proof records' : 'All proof records' }}
                </p>
            </div>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Total Proof Records</p>
            <p class="mt-2 text-2xl font-bold text-ui-text">{{ number_format($stats['total']) }}</p>
            <p class="mt-1 text-xs text-ui-subtext">Blockchain verification entries</p>
        </div>

        <div class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Confirmed</p>
            <p class="mt-2 text-2xl font-bold text-ui-success">{{ number_format($stats['confirmed']) }}</p>
            <p class="mt-1 text-xs text-emerald-600">Recorded on Morph</p>
        </div>

        <div class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Pending</p>
            <p class="mt-2 text-2xl font-bold text-ui-warning">{{ number_format($stats['pending']) }}</p>
            <p class="mt-1 text-xs text-amber-600">Awaiting confirmation</p>
        </div>

        <div class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Failed</p>
            <p class="mt-2 text-2xl font-bold text-ui-danger">{{ number_format($stats['failed']) }}</p>
            <p class="mt-1 text-xs text-rose-600">Needs review</p>
        </div>

        <div class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Hashes Issued</p>
            <p class="mt-2 text-2xl font-bold text-ui-proof">{{ number_format($stats['with_hash']) }}</p>
            <p class="mt-1 text-xs text-cyan-600">Explorer-ready proof records</p>
        </div>
    </div>

    <section class="rounded-2xl border border-cyan-100 bg-cyan-50 p-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-cyan-800">
                    Blockchain Proof Layer
                </p>

                <p class="mt-1 max-w-3xl text-sm leading-6 text-cyan-700">
                    EduNexUs records assistance workflow proofs on Morph so administrators and auditors can verify claim and settlement activity without exposing blockchain complexity to normal users.
                </p>
            </div>

            <x-status-badge status="Morph Integrated" tone="proof" />
        </div>
    </section>

    <section class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60 sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-base font-semibold text-ui-text">
                    Filter Verification Records
                </h2>

                <p class="mt-1 text-sm leading-6 text-ui-subtext">
                    Narrow proof records by workflow type or blockchain status for faster audit review.
                </p>
            </div>

            @if($hasFilters)
                <a href="{{ route('admin.blockchain-transactions.index') }}"
                   class="inline-flex min-h-10 items-center justify-center rounded-xl border border-ui-border px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-ui-canvas">
                    Clear filters
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('admin.blockchain-transactions.index') }}" class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-[1fr_1fr_auto]">
            <div>
                <label for="transaction_type" class="block text-sm font-semibold text-slate-700">
                    Transaction Type
                </label>

                <select id="transaction_type"
                        name="transaction_type"
                        class="mt-2 w-full rounded-xl border-slate-200 text-sm text-slate-700 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                    <option value="">All transaction types</option>

                    @foreach($transactionTypeOptions as $transactionType)
                        <option value="{{ $transactionType }}" @selected(($filters['transaction_type'] ?? null) === $transactionType)>
                            {{ $transactionType }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="blockchain_status" class="block text-sm font-semibold text-slate-700">
                    Blockchain Status
                </label>

                <select id="blockchain_status"
                        name="blockchain_status"
                        class="mt-2 w-full rounded-xl border-slate-200 text-sm text-slate-700 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                    <option value="">All statuses</option>

                    @foreach($statusOptions as $status)
                        <option value="{{ $status }}" @selected(($filters['blockchain_status'] ?? null) === $status)>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-ui-proof/20 bg-ui-proof/10 px-5 py-2.5 text-sm font-semibold text-ui-proof shadow-sm transition hover:bg-ui-proof/15 lg:w-auto">
                    Apply Filters
                </button>
            </div>
        </form>
    </section>

    <x-table-card
        title="Verification Records"
        description="Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} records.">
        <x-slot:actions>
            <x-status-badge status="Audit-ready proofs" tone="proof" />
        </x-slot:actions>

        <div class="hidden xl:block">
            <table class="min-w-full divide-y divide-ui-border text-sm">
                <thead class="bg-ui-canvas/70">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Proof Type</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Reference</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Transaction Hash</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Status</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Recorded</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-ui-subtext">Proof</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-ui-border/70 bg-ui-surface">
                    @forelse($transactions as $transaction)
                        @php
                            $hash = $transaction->transaction_hash;
                            $hasRealHash = $hash && str_starts_with($hash, '0x');
                            $shortHash = $hasRealHash
                                ? substr($hash, 0, 10) . '...' . substr($hash, -8)
                                : ($hash ?? 'Not available');
                        @endphp

                        <tr class="transition hover:bg-ui-canvas/60">
                            <td class="px-5 py-5 align-top">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-ui-canvas text-ui-action ring-1 ring-ui-border">
                                        <x-icon :name="$typeIcons($transaction->transaction_type)" size="h-5 w-5" />
                                    </div>

                                    <div>
                                        <x-status-badge :status="$transaction->transaction_type" :tone="$typeTone($transaction->transaction_type)" />

                                        <p class="mt-2 text-xs text-ui-subtext">
                                            Smart contract proof
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <p class="font-mono text-xs font-semibold text-ui-text">
                                    {{ $transaction->reference_code ?? 'N/A' }}
                                </p>

                                <p class="mt-1 text-xs text-ui-subtext">
                                    Reference #{{ $transaction->reference_id }}
                                </p>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <div class="inline-flex max-w-xs flex-col rounded-xl border border-ui-border bg-ui-canvas/70 px-3 py-2">
                                    <span class="break-all font-mono text-xs font-semibold text-ui-text" title="{{ $hash ?? 'No transaction hash recorded' }}">
                                        {{ $shortHash }}
                                    </span>

                                    <span class="mt-1 text-xs text-ui-subtext">
                                        {{ $hasRealHash ? 'Morph transaction hash' : 'No explorer hash available' }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <x-status-badge :status="$transaction->blockchain_status" :tone="$statusTone($transaction->blockchain_status)" />
                            </td>

                            <td class="px-5 py-5 align-top">
                                <p class="text-sm font-medium text-slate-700">
                                    {{ $transaction->recorded_at?->format('M d, Y') ?? 'Not recorded' }}
                                </p>

                                <p class="mt-1 text-xs text-ui-subtext">
                                    {{ $transaction->recorded_at?->format('g:i A') ?? 'Awaiting timestamp' }}
                                </p>
                            </td>

                            <td class="px-5 py-5 text-right align-top">
                                @if($hasRealHash)
                                    <a href="{{ $explorerBaseUrl . $hash }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="inline-flex min-h-10 items-center justify-center rounded-xl bg-ui-proof px-4 py-2 text-xs font-semibold text-white transition hover:bg-cyan-700">
                                        View on Morph
                                    </a>
                                @elseif($transaction->blockchain_status === 'Pending')
                                    <form method="POST"
                                          action="{{ route('admin.blockchain-transactions.confirm', $transaction) }}"
                                          data-confirm
                                          data-confirm-title="Confirm blockchain proof?"
                                          data-confirm-message="This will mark the selected blockchain proof record as confirmed in EduNexUs."
                                          data-confirm-button="Confirm proof"
                                          data-confirm-tone="warning"
                                          data-loading-text="Confirming proof..."
                                          data-loader-title="Confirming blockchain proof..."
                                          data-loader-message="Updating the Morph proof record status in the EduNexUs audit console.">
                                        @csrf

                                        <button type="submit"
                                                class="inline-flex min-h-10 items-center justify-center rounded-xl border border-cyan-200 px-4 py-2 text-xs font-semibold text-cyan-700 transition hover:bg-cyan-50">
                                            Confirm
                                        </button>
                                    </form>
                                @else
                                    <span class="text-sm text-ui-subtext">
                                        No proof link
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="mx-auto max-w-md">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-ui-canvas text-ui-subtext">
                                        <x-icon name="link" size="h-8 w-8" />
                                    </div>

                                    <h3 class="mt-5 text-lg font-semibold text-ui-text">
                                        No verification records found
                                    </h3>

                                    <p class="mt-2 text-sm text-ui-subtext">
                                        {{ $hasFilters
                                            ? 'No blockchain proof records match the selected filters. Clear filters to return to the full console.'
                                            : 'Records will appear after a merchant processes a valid claim and Morph proof recording runs.' }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-ui-border/80 xl:hidden">
            @forelse($transactions as $transaction)
                @php
                    $hash = $transaction->transaction_hash;
                    $hasRealHash = $hash && str_starts_with($hash, '0x');
                    $shortHash = $hasRealHash
                        ? substr($hash, 0, 10) . '...' . substr($hash, -8)
                        : ($hash ?? 'Not available');
                @endphp

                <article class="p-4 sm:p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex min-w-0 items-start gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-ui-canvas text-ui-action ring-1 ring-ui-border">
                                <x-icon :name="$typeIcons($transaction->transaction_type)" size="h-5 w-5" />
                            </div>

                            <div class="min-w-0">
                                <div class="flex flex-wrap gap-2">
                                    <x-status-badge :status="$transaction->transaction_type" :tone="$typeTone($transaction->transaction_type)" />
                                    <x-status-badge :status="$transaction->blockchain_status" :tone="$statusTone($transaction->blockchain_status)" />
                                </div>

                                <p class="mt-2 font-mono text-xs font-semibold text-ui-text">
                                    {{ $transaction->reference_code ?? 'N/A' }}
                                </p>

                                <p class="mt-1 text-xs text-ui-subtext">
                                    Reference #{{ $transaction->reference_id }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <dl class="mt-4 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                        <div class="rounded-xl bg-ui-canvas/70 p-3 sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext">Transaction Hash</dt>
                            <dd class="mt-1 break-all font-mono text-xs font-semibold text-ui-text" title="{{ $hash ?? 'No transaction hash recorded' }}">
                                {{ $shortHash }}
                            </dd>
                            <dd class="mt-1 text-xs text-ui-subtext">
                                {{ $hasRealHash ? 'Morph transaction hash' : 'No explorer hash available' }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-ui-canvas/70 p-3">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext">Recorded</dt>
                            <dd class="mt-1 font-semibold text-ui-text">
                                {{ $transaction->recorded_at?->format('M d, Y') ?? 'Not recorded' }}
                            </dd>
                            <dd class="mt-1 text-xs text-ui-subtext">
                                {{ $transaction->recorded_at?->format('g:i A') ?? 'Awaiting timestamp' }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-ui-canvas/70 p-3">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext">Proof Layer</dt>
                            <dd class="mt-1 font-semibold text-ui-text">
                                Smart contract proof
                            </dd>
                            <dd class="mt-1 text-xs text-ui-subtext">
                                {{ $hasRealHash ? 'Explorer-ready record' : 'Console verification record' }}
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-4">
                        @if($hasRealHash)
                            <a href="{{ $explorerBaseUrl . $hash }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-ui-proof px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-cyan-700 sm:w-auto">
                                View on Morph
                            </a>
                        @elseif($transaction->blockchain_status === 'Pending')
                            <form method="POST"
                                  action="{{ route('admin.blockchain-transactions.confirm', $transaction) }}"
                                  data-confirm
                                  data-confirm-title="Confirm blockchain proof?"
                                  data-confirm-message="This will mark the selected blockchain proof record as confirmed in EduNexUs."
                                  data-confirm-button="Confirm proof"
                                  data-confirm-tone="warning"
                                  data-loading-text="Confirming proof..."
                                  data-loader-title="Confirming blockchain proof..."
                                  data-loader-message="Updating the Morph proof record status in the EduNexUs audit console.">
                                @csrf

                                <button type="submit"
                                        class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-cyan-200 px-4 py-2.5 text-sm font-semibold text-cyan-700 transition hover:bg-cyan-50 sm:w-auto">
                                    Confirm
                                </button>
                            </form>
                        @else
                            <span class="inline-flex min-h-10 items-center rounded-xl bg-ui-canvas px-4 py-2 text-sm font-semibold text-ui-subtext">
                                No proof link
                            </span>
                        @endif
                    </div>
                </article>
            @empty
                <div class="px-4 py-14 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-ui-canvas text-ui-subtext">
                        <x-icon name="link" size="h-8 w-8" />
                    </div>

                    <h3 class="mt-5 text-lg font-semibold text-ui-text">
                        No verification records found
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm text-ui-subtext">
                        {{ $hasFilters
                            ? 'No blockchain proof records match the selected filters. Clear filters to return to the full console.'
                            : 'Records will appear after a merchant processes a valid claim and Morph proof recording runs.' }}
                    </p>
                </div>
            @endforelse
        </div>

        <x-slot:footer>
            <div class="flex flex-col items-center justify-center gap-3 text-center">
                <p class="text-sm text-ui-subtext">
                    Page {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}
                </p>

                <div class="flex max-w-full justify-center overflow-x-auto">
                    {{ $transactions->links() }}
                </div>
            </div>
        </x-slot:footer>
    </x-table-card>
</div>

@endsection
