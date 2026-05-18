@extends('layouts.dashboard')

@section('title', 'Morph Verification Console')

@section('content')

@php
    $hasFilters = filled($filters['blockchain_status'] ?? null) || filled($filters['transaction_type'] ?? null);
    $explorerBaseUrl = 'https://explorer-hoodi.morph.network/tx/';

    $statusClasses = function ($status) {
        return match ($status) {
            'Confirmed' => 'bg-emerald-100 text-emerald-700',
            'Failed' => 'bg-red-100 text-red-700',
            default => 'bg-yellow-100 text-yellow-700',
        };
    };

    $typeClasses = function ($type) {
        return match ($type) {
            'Approval' => 'bg-teal-100 text-teal-700',
            'Settlement' => 'bg-cyan-100 text-cyan-700',
            default => 'bg-indigo-100 text-indigo-700',
        };
    };
@endphp

<div class="max-w-7xl space-y-6">

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-cyan-700">
                Morph Proof Layer
            </p>

            <h1 class="mt-2 text-3xl font-bold text-slate-800">
                Morph Verification Console
            </h1>

            <p class="mt-2 max-w-3xl text-slate-500">
                Monitor Morph blockchain proof records generated from cooperative assistance validation and settlement workflows.
            </p>
        </div>

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
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
            <p class="font-semibold text-emerald-800">
                {{ session('success') }}
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Total Proof Records
            </p>

            <p class="mt-2 text-2xl font-bold text-slate-800">
                {{ number_format($stats['total']) }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Blockchain verification entries
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Confirmed
            </p>

            <p class="mt-2 text-2xl font-bold text-emerald-700">
                {{ number_format($stats['confirmed']) }}
            </p>

            <p class="mt-1 text-xs text-emerald-600">
                Recorded on Morph
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Pending
            </p>

            <p class="mt-2 text-2xl font-bold text-yellow-700">
                {{ number_format($stats['pending']) }}
            </p>

            <p class="mt-1 text-xs text-yellow-600">
                Awaiting confirmation
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Failed
            </p>

            <p class="mt-2 text-2xl font-bold text-red-700">
                {{ number_format($stats['failed']) }}
            </p>

            <p class="mt-1 text-xs text-red-600">
                Needs review
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Hashes Issued
            </p>

            <p class="mt-2 text-2xl font-bold text-cyan-700">
                {{ number_format($stats['with_hash']) }}
            </p>

            <p class="mt-1 text-xs text-cyan-600">
                Explorer-ready proof records
            </p>
        </div>

    </div>

    <div class="rounded-2xl border border-cyan-100 bg-cyan-50 p-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-cyan-800">
                    Blockchain Proof Layer
                </p>

                <p class="mt-1 max-w-3xl text-sm text-cyan-700">
                    EduNexUs records assistance workflow proofs on Morph so administrators and auditors can verify claim and settlement activity without exposing blockchain complexity to normal users.
                </p>
            </div>

            <span class="inline-flex w-fit rounded-full border border-cyan-200 bg-white px-3 py-1 text-xs font-semibold text-cyan-700">
                Morph Integrated
            </span>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">
                    Filter Verification Records
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Narrow proof records by workflow type or blockchain status for faster audit review.
                </p>
            </div>

            @if($hasFilters)
                <a href="{{ route('admin.blockchain-transactions.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
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
                        class="inline-flex w-full items-center justify-center rounded-xl bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-cyan-700 lg:w-auto">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-6 py-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">
                        Verification Records
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} records.
                    </p>
                </div>

                <span class="inline-flex w-fit rounded-full border border-cyan-200 bg-cyan-50 px-3 py-1 text-xs font-semibold text-cyan-700">
                    Audit-ready proofs
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Proof Type
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Reference
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Transaction Hash
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Recorded
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Proof
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($transactions as $transaction)
                        @php
                            $hash = $transaction->transaction_hash;
                            $hasRealHash = $hash && str_starts_with($hash, '0x');
                            $shortHash = $hasRealHash
                                ? substr($hash, 0, 10) . '...' . substr($hash, -8)
                                : ($hash ?? 'Not available');
                        @endphp

                        <tr class="transition hover:bg-slate-50">
                            <td class="px-6 py-5 align-top">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-xs font-bold {{ $typeClasses($transaction->transaction_type) }}">
                                        {{ str($transaction->transaction_type)->substr(0, 2)->upper() }}
                                    </div>

                                    <div>
                                        <p class="font-semibold text-slate-800">
                                            {{ $transaction->transaction_type }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            Smart contract proof
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-5 align-top">
                                <p class="font-mono text-xs font-semibold text-slate-700">
                                    {{ $transaction->reference_code ?? 'N/A' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Reference #{{ $transaction->reference_id }}
                                </p>
                            </td>

                            <td class="px-6 py-5 align-top">
                                <div class="inline-flex max-w-xs flex-col rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                                    <span class="font-mono text-xs font-semibold text-slate-700" title="{{ $hash ?? 'No transaction hash recorded' }}">
                                        {{ $shortHash }}
                                    </span>

                                    <span class="mt-1 text-xs text-slate-400">
                                        {{ $hasRealHash ? 'Morph transaction hash' : 'No explorer hash available' }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-5 align-top">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses($transaction->blockchain_status) }}">
                                    {{ $transaction->blockchain_status }}
                                </span>
                            </td>

                            <td class="px-6 py-5 align-top">
                                <p class="text-sm font-medium text-slate-700">
                                    {{ $transaction->recorded_at?->format('M d, Y') ?? 'Not recorded' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $transaction->recorded_at?->format('g:i A') ?? 'Awaiting timestamp' }}
                                </p>
                            </td>

                            <td class="px-6 py-5 align-top">
                                @if($hasRealHash)
                                    <a href="{{ $explorerBaseUrl . $hash }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="inline-flex items-center rounded-xl bg-cyan-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-cyan-700">
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
                                                class="inline-flex items-center rounded-xl border border-cyan-200 px-4 py-2 text-xs font-semibold text-cyan-700 transition hover:bg-cyan-50">
                                            Confirm
                                        </button>
                                    </form>
                                @else
                                    <span class="text-sm text-slate-400">
                                        No proof link
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="mx-auto max-w-md">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-sm font-bold text-slate-500">
                                        TX
                                    </div>

                                    <h3 class="mt-5 text-lg font-semibold text-slate-700">
                                        No verification records found
                                    </h3>

                                    <p class="mt-2 text-sm text-slate-500">
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

        <div class="border-t border-slate-100 bg-slate-50 px-6 py-4">
            <div class="flex flex-col items-center justify-center gap-3 text-center">
                <p class="text-sm text-slate-500">
                    Page {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}
                </p>

                <div class="flex justify-center">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
