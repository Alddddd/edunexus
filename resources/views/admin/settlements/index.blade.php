@extends('layouts.dashboard')

@section('title', 'Merchant Settlements')

@section('content')

@php
    $hasFilters = filled($filters['status'] ?? null);

    $statusClasses = function ($status) {
        return $status === 'Settled'
            ? 'bg-emerald-100 text-emerald-700'
            : 'bg-yellow-100 text-yellow-700';
    };
@endphp

<div class="max-w-7xl space-y-6">

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-teal-700">
                Settlement Operations
            </p>

            <h1 class="mt-2 text-3xl font-bold text-slate-800">
                Merchant Settlement Console
            </h1>

            <p class="mt-2 max-w-3xl text-slate-500">
                Track cooperative reimbursements owed to merchants after assistance claims are processed.
            </p>
        </div>

        <div class="rounded-2xl border border-teal-100 bg-teal-50 px-5 py-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-teal-700">
                Current View
            </p>

            <p class="mt-1 text-2xl font-bold text-teal-800">
                {{ number_format($settlements->total()) }}
            </p>

            <p class="text-xs text-teal-700">
                {{ $hasFilters ? $filters['status'] . ' settlement records' : 'All settlement records' }}
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
                Total Records
            </p>

            <p class="mt-2 text-2xl font-bold text-slate-800">
                {{ number_format($stats['total']) }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Generated from merchant claims
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
                Awaiting cooperative payment
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Settled
            </p>

            <p class="mt-2 text-2xl font-bold text-emerald-700">
                {{ number_format($stats['settled']) }}
            </p>

            <p class="mt-1 text-xs text-emerald-600">
                Merchant already reimbursed
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Pending Value
            </p>

            <p class="mt-2 text-2xl font-bold text-slate-800">
                PHP {{ number_format($stats['pending_amount'], 2) }}
            </p>

            <p class="mt-1 text-xs text-yellow-600">
                Outstanding reimbursement value
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Settled Value
            </p>

            <p class="mt-2 text-2xl font-bold text-slate-800">
                PHP {{ number_format($stats['settled_amount'], 2) }}
            </p>

            <p class="mt-1 text-xs text-teal-600">
                Completed merchant reimbursements
            </p>
        </div>

    </div>

    <div class="rounded-2xl border border-teal-100 bg-teal-50 p-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-teal-800">
                    Settlement Lifecycle
                </p>

                <p class="mt-1 max-w-3xl text-sm text-teal-700">
                    When a merchant processes a valid claim, EduNexUs creates a pending settlement. Once the cooperative reimburses the merchant, the record is marked as settled.
                </p>
            </div>

            <span class="inline-flex w-fit rounded-full border border-teal-200 bg-white px-3 py-1 text-xs font-semibold text-teal-700">
                Reimbursement Tracking
            </span>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">
                    Filter Settlement Records
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Monitor pending reimbursements or review completed settlement history.
                </p>
            </div>

            @if($hasFilters)
                <a href="{{ route('admin.settlements.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Clear filters
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('admin.settlements.index') }}" class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-[1fr_auto]">
            <div>
                <label for="status" class="block text-sm font-semibold text-slate-700">
                    Settlement Status
                </label>

                <select id="status"
                        name="status"
                        class="mt-2 w-full rounded-xl border-slate-200 text-sm text-slate-700 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    <option value="">All statuses</option>
                    <option value="Pending" @selected(($filters['status'] ?? null) === 'Pending')>
                        Pending
                    </option>
                    <option value="Settled" @selected(($filters['status'] ?? null) === 'Settled')>
                        Settled
                    </option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-700 lg:w-auto">
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-6 py-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">
                        Settlement Records
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Showing {{ $settlements->firstItem() ?? 0 }} to {{ $settlements->lastItem() ?? 0 }} of {{ $settlements->total() }} records.
                    </p>
                </div>

                <span class="inline-flex w-fit rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                    Merchant reimbursement console
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Reference
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Merchant
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Member / Program
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Amount
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Timeline
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($settlements as $settlement)
                        @php
                            $merchantProfile = $settlement->merchant?->merchantProfile;
                        @endphp

                        <tr class="transition hover:bg-slate-50">
                            <td class="px-6 py-5 align-top">
                                <p class="font-mono text-xs font-semibold text-slate-700">
                                    {{ $settlement->assistanceRequest->reference_code ?? 'N/A' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Settlement #{{ $settlement->id }}
                                </p>
                            </td>

                            <td class="px-6 py-5 align-top">
                                <p class="font-semibold text-slate-800">
                                    {{ $merchantProfile->business_name ?? $settlement->merchant->name ?? 'N/A' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $merchantProfile->merchant_category ?? 'Merchant account' }}
                                </p>

                                @if($merchantProfile?->status)
                                    <span class="mt-2 inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                        {{ $merchantProfile->status }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-5 align-top">
                                <p class="font-medium text-slate-700">
                                    {{ $settlement->assistanceRequest->member->name ?? 'N/A' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $settlement->assistanceRequest->program->program_name ?? 'Assistance program' }}
                                </p>
                            </td>

                            <td class="px-6 py-5 align-top">
                                <p class="font-semibold text-slate-800">
                                    PHP {{ number_format($settlement->amount, 2) }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Merchant reimbursement
                                </p>
                            </td>

                            <td class="px-6 py-5 align-top">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses($settlement->status) }}">
                                    {{ $settlement->status }}
                                </span>
                            </td>

                            <td class="px-6 py-5 align-top">
                                <p class="text-sm font-medium text-slate-700">
                                    Created {{ $settlement->created_at->format('M d, Y') }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $settlement->created_at->format('g:i A') }}
                                </p>

                                <p class="mt-3 text-sm font-medium {{ $settlement->settled_at ? 'text-emerald-700' : 'text-slate-500' }}">
                                    {{ $settlement->settled_at ? 'Settled ' . $settlement->settled_at->format('M d, Y') : 'Not settled yet' }}
                                </p>

                                @if($settlement->settled_at)
                                    <p class="mt-1 text-xs text-emerald-600">
                                        {{ $settlement->settled_at->format('g:i A') }}
                                    </p>
                                @endif
                            </td>

                            <td class="px-6 py-5 align-top">
                                @if($settlement->status === 'Pending')
                                    <form method="POST"
                                          action="{{ route('admin.settlements.settle', $settlement) }}"
                                          data-confirm
                                          data-confirm-title="Mark settlement as settled?"
                                          data-confirm-message="This will complete the merchant reimbursement record and notify the merchant."
                                          data-confirm-button="Mark settled"
                                          data-confirm-tone="success"
                                          data-loading-text="Marking settled..."
                                          data-loader-title="Completing settlement..."
                                          data-loader-message="Finalizing the merchant reimbursement record and updating settlement visibility.">
                                        @csrf

                                        <button type="submit"
                                                class="inline-flex items-center rounded-xl bg-teal-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-teal-700">
                                            Mark Settled
                                        </button>
                                    </form>
                                @else
                                    <span class="text-sm font-medium text-slate-400">
                                        Completed
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="mx-auto max-w-md">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-sm font-bold text-slate-500">
                                        SET
                                    </div>

                                    <h3 class="mt-5 text-lg font-semibold text-slate-700">
                                        No settlement records found
                                    </h3>

                                    <p class="mt-2 text-sm text-slate-500">
                                        {{ $hasFilters
                                            ? 'No settlement records match the selected status. Clear filters to return to the full console.'
                                            : 'Settlement records appear after merchants process valid claims.' }}
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
                    Page {{ $settlements->currentPage() }} of {{ $settlements->lastPage() }}
                </p>

                <div class="flex justify-center">
                    {{ $settlements->links() }}
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
