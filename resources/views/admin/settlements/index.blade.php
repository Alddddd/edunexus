@extends('layouts.dashboard')

@section('title', 'Merchant Settlements')

@section('content')

@php
    $hasFilters = filled($filters['status'] ?? null);

    $statusLabel = function ($status) {
        return match ($status) {
            'Pending' => 'Ready for Release',
            'Settled' => 'Released',
            default => $status,
        };
    };

    $statusTone = function ($status) {
        return match ($status) {
            'Settled' => 'settled',
            'Disputed' => 'danger',
            default => 'warning',
        };
    };

    $proofPayload = function ($proof) {
        return $proof ? (json_decode($proof->payload ?: '[]', true) ?: []) : [];
    };
@endphp

<div class="w-full min-w-0 max-w-7xl space-y-6">
    <x-page-header
        title="Merchant Settlement Console"
        eyebrow="Settlement Operations"
        description="Track cooperative reimbursements owed to merchants after assistance claims are processed.">
        <x-slot:actions>
            <div class="rounded-2xl border border-teal-100 bg-teal-50 px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-teal-700">
                    Current View
                </p>

                <p class="mt-1 text-2xl font-bold text-teal-800">
                    {{ number_format($settlements->total()) }}
                </p>

                <p class="text-xs text-teal-700">
                    {{ $hasFilters ? $statusLabel($filters['status']) . ' settlement records' : 'All settlement records' }}
                </p>
            </div>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Total Reimbursements</p>
            <p class="mt-2 text-2xl font-bold text-ui-text">{{ number_format($stats['total']) }}</p>
            <p class="mt-1 text-xs text-ui-subtext">Generated from merchant claims</p>
        </div>

        <div class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Pending Settlements</p>
            <p class="mt-2 text-2xl font-bold text-ui-warning">{{ number_format($stats['pending']) }}</p>
            <p class="mt-1 text-xs text-amber-600">Ready for release review</p>
        </div>

        <div class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Released / Completed</p>
            <p class="mt-2 text-2xl font-bold text-ui-success">{{ number_format($stats['released']) }}</p>
            <p class="mt-1 text-xs text-emerald-600">Merchant reimbursements released</p>
        </div>

        <div class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Pending Value</p>
            <p class="mt-2 text-2xl font-bold text-ui-text">&#8369;{{ number_format($stats['pending_amount'], 2) }}</p>
            <p class="mt-1 text-xs text-amber-600">Outstanding reimbursement value</p>
        </div>

        <div class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Released Value</p>
            <p class="mt-2 text-2xl font-bold text-ui-text">&#8369;{{ number_format($stats['released_amount'], 2) }}</p>
            <p class="mt-1 text-xs text-teal-600">Completed merchant reimbursements</p>
        </div>
    </div>

    <section class="rounded-2xl border border-teal-100 bg-teal-50 p-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-teal-800">
                    Settlement Lifecycle
                </p>

                <p class="mt-1 max-w-3xl text-sm leading-6 text-teal-700">
                    When a merchant processes a valid claim, EduNexUs generates a reimbursement record. Admins review and release the settlement while claim proof remains visible for audit.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <x-status-badge status="Reimbursement Tracking" tone="proof" />
                <x-status-badge status="Settlement audit-ready" tone="success" />
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60 sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-base font-semibold text-ui-text">
                    Filter Settlement Records
                </h2>

                <p class="mt-1 text-sm leading-6 text-ui-subtext">
                    Monitor pending reimbursements or review completed settlement history.
                </p>
            </div>

            @if($hasFilters)
                <a href="{{ route('admin.settlements.index') }}"
                   class="inline-flex min-h-10 items-center justify-center rounded-xl border border-ui-border px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-ui-canvas">
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
                        Ready for Release
                    </option>
                    <option value="Settled" @selected(($filters['status'] ?? null) === 'Settled')>
                        Released
                    </option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-ui-action/20 bg-ui-action/10 px-5 py-2.5 text-sm font-semibold text-ui-action shadow-sm transition hover:bg-ui-action/15 lg:w-auto">
                    Apply Filter
                </button>
            </div>
        </form>
    </section>

    <x-table-card
        title="Settlement Records"
        description="Showing {{ $settlements->firstItem() ?? 0 }} to {{ $settlements->lastItem() ?? 0 }} of {{ $settlements->total() }} records.">
        <x-slot:actions>
            <x-status-badge status="Merchant reimbursement console" tone="neutral" />
        </x-slot:actions>

        <div class="hidden xl:block">
            <table class="min-w-full divide-y divide-ui-border text-sm">
                <thead class="bg-ui-canvas/70">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Reference</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Merchant</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Member / Program</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Amount</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Status</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Timeline</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-ui-subtext">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-ui-border/70 bg-ui-surface">
                    @forelse($settlements as $settlement)
                        @php
                            $merchantProfile = $settlement->merchant?->merchantProfile;
                            $proofRecord = $proofRecords[$settlement->assistance_request_id] ?? null;
                            $proofData = $proofPayload($proofRecord);
                            $proofHash = $proofData['proof_hash'] ?? null;
                            $proofStatus = $proofRecord?->blockchain_status ?? 'Not recorded';
                            $isReleased = $settlement->status === 'Settled';
                        @endphp

                        <tr class="transition hover:bg-ui-canvas/60">
                            <td class="px-5 py-5 align-top">
                                <p class="font-mono text-xs font-semibold text-ui-text">
                                    {{ $settlement->assistanceRequest->reference_code ?? 'N/A' }}
                                </p>

                                <p class="mt-1 text-xs text-ui-subtext">
                                    Settlement #{{ $settlement->id }}
                                </p>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <p class="font-semibold text-ui-text">
                                    {{ $merchantProfile->business_name ?? $settlement->merchant->name ?? 'N/A' }}
                                </p>

                                <p class="mt-1 text-xs text-ui-subtext">
                                    {{ $merchantProfile->merchant_category ?? 'Merchant account' }}
                                </p>

                                @if($merchantProfile?->status)
                                    <x-status-badge class="mt-2" :status="$merchantProfile->status" :tone="$merchantProfile->status === 'Active' ? 'active' : 'neutral'" size="xs" />
                                @endif
                            </td>

                            <td class="px-5 py-5 align-top">
                                <p class="font-medium text-slate-700">
                                    {{ $settlement->assistanceRequest->member->name ?? 'N/A' }}
                                </p>

                                <p class="mt-1 text-xs text-ui-subtext">
                                    {{ $settlement->assistanceRequest->program->program_name ?? 'Assistance program' }}
                                </p>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <p class="text-base font-bold text-ui-text">
                                    &#8369;{{ number_format($settlement->amount, 2) }}
                                </p>

                                <p class="mt-1 text-xs text-ui-subtext">
                                    Merchant reimbursement
                                </p>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <div class="space-y-2">
                                    <x-status-badge
                                        :status="$statusLabel($settlement->status)"
                                        :tone="$statusTone($settlement->status)" />

                                    <x-status-badge
                                        :status="$proofStatus === 'Not recorded' ? 'Proof Pending' : 'Morph Proof ' . $proofStatus"
                                        :tone="$proofStatus === 'Confirmed' ? 'success' : ($proofStatus === 'Failed' ? 'danger' : 'warning')"
                                        size="xs" />
                                </div>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full bg-ui-success"></span>
                                        <span class="text-xs font-semibold text-slate-700">Claim processed</span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full bg-ui-success"></span>
                                        <span class="text-xs font-semibold text-slate-700">Settlement generated</span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full {{ $isReleased ? 'bg-ui-success' : 'bg-ui-warning' }}"></span>
                                        <span class="text-xs font-semibold {{ $isReleased ? 'text-slate-700' : 'text-ui-warning' }}">
                                            {{ $isReleased ? 'Settlement released' : 'Ready for release' }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full {{ $proofRecord ? 'bg-ui-proof' : 'bg-slate-300' }}"></span>
                                        <span class="text-xs font-semibold {{ $proofRecord ? 'text-slate-700' : 'text-ui-subtext' }}">
                                            {{ $proofRecord ? 'Proof recorded' : 'Proof pending' }}
                                        </span>
                                    </div>
                                </div>

                                @if($proofHash)
                                    <div class="mt-3 rounded-xl border border-cyan-100 bg-cyan-50 px-3 py-2">
                                        <p class="text-xs font-semibold text-cyan-800">Proof Bundle Hash</p>
                                        <p class="mt-1 break-all font-mono text-[11px] text-cyan-700" title="{{ $proofHash }}">
                                            {{ substr($proofHash, 0, 16) }}...{{ substr($proofHash, -12) }}
                                        </p>
                                    </div>
                                @endif
                            </td>

                            <td class="px-5 py-5 text-right align-top">
                                @if($settlement->status === 'Pending')
                                    <form method="POST"
                                          action="{{ route('admin.settlements.settle', $settlement) }}"
                                          data-confirm
                                          data-confirm-title="Release merchant settlement?"
                                          data-confirm-message="This records the cooperative reimbursement as released and notifies the merchant. No wallet or token transfer will be executed."
                                          data-confirm-button="Release settlement"
                                          data-confirm-tone="success"
                                          data-loading-text="Releasing settlement..."
                                          data-loader-title="Releasing settlement..."
                                          data-loader-message="Updating the reimbursement lifecycle and notifying the merchant.">
                                        @csrf

                                        <button type="submit"
                                                class="inline-flex min-h-10 items-center justify-center rounded-xl bg-ui-action px-4 py-2 text-xs font-semibold text-white transition hover:bg-ui-anchor">
                                            Release Settlement
                                        </button>
                                    </form>
                                @else
                                    <span class="text-sm font-medium text-ui-subtext">
                                        Released {{ $settlement->settled_at?->format('M d, Y') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="mx-auto max-w-md">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-ui-canvas text-ui-subtext">
                                        <x-icon name="credit-card" size="h-8 w-8" />
                                    </div>

                                    <h3 class="mt-5 text-lg font-semibold text-ui-text">
                                        No settlement records found
                                    </h3>

                                    <p class="mt-2 text-sm text-ui-subtext">
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

        <div class="divide-y divide-ui-border/80 xl:hidden">
            @forelse($settlements as $settlement)
                @php
                    $merchantProfile = $settlement->merchant?->merchantProfile;
                    $proofRecord = $proofRecords[$settlement->assistance_request_id] ?? null;
                    $proofData = $proofPayload($proofRecord);
                    $proofHash = $proofData['proof_hash'] ?? null;
                    $proofStatus = $proofRecord?->blockchain_status ?? 'Not recorded';
                    $isReleased = $settlement->status === 'Settled';
                @endphp

                <article class="p-4 sm:p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <p class="font-mono text-xs font-semibold text-ui-subtext">
                                {{ $settlement->assistanceRequest->reference_code ?? 'N/A' }}
                            </p>

                            <p class="mt-1 text-lg font-bold text-ui-text">
                                &#8369;{{ number_format($settlement->amount, 2) }}
                            </p>

                            <p class="mt-1 text-sm text-ui-subtext">
                                Settlement #{{ $settlement->id }}
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2 sm:justify-end">
                            <x-status-badge
                                :status="$statusLabel($settlement->status)"
                                :tone="$statusTone($settlement->status)" />

                            <x-status-badge
                                :status="$proofStatus === 'Not recorded' ? 'Proof Pending' : 'Morph Proof ' . $proofStatus"
                                :tone="$proofStatus === 'Confirmed' ? 'success' : ($proofStatus === 'Failed' ? 'danger' : 'warning')" />
                        </div>
                    </div>

                    <dl class="mt-4 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                        <div class="rounded-xl bg-ui-canvas/70 p-3">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext">Merchant</dt>
                            <dd class="mt-1 font-semibold text-ui-text">
                                {{ $merchantProfile->business_name ?? $settlement->merchant->name ?? 'N/A' }}
                            </dd>
                            <dd class="mt-1 text-xs text-ui-subtext">
                                {{ $merchantProfile->merchant_category ?? 'Merchant account' }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-ui-canvas/70 p-3">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext">Member / Program</dt>
                            <dd class="mt-1 font-semibold text-ui-text">
                                {{ $settlement->assistanceRequest->member->name ?? 'N/A' }}
                            </dd>
                            <dd class="mt-1 text-xs text-ui-subtext">
                                {{ $settlement->assistanceRequest->program->program_name ?? 'Assistance program' }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-ui-canvas/70 p-3">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext">Created</dt>
                            <dd class="mt-1 font-semibold text-ui-text">
                                {{ $settlement->created_at->format('M d, Y') }}
                            </dd>
                            <dd class="mt-1 text-xs text-ui-subtext">
                                {{ $settlement->created_at->format('g:i A') }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-ui-canvas/70 p-3">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext">Lifecycle</dt>
                            <dd class="mt-2 space-y-2">
                                <span class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                                    <span class="h-2.5 w-2.5 rounded-full bg-ui-success"></span>
                                    Claim processed
                                </span>
                                <span class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                                    <span class="h-2.5 w-2.5 rounded-full bg-ui-success"></span>
                                    Settlement generated
                                </span>
                                <span class="flex items-center gap-2 text-xs font-semibold {{ $isReleased ? 'text-slate-700' : 'text-ui-warning' }}">
                                    <span class="h-2.5 w-2.5 rounded-full {{ $isReleased ? 'bg-ui-success' : 'bg-ui-warning' }}"></span>
                                    {{ $isReleased ? 'Settlement released' : 'Ready for release' }}
                                </span>
                                <span class="flex items-center gap-2 text-xs font-semibold {{ $proofRecord ? 'text-slate-700' : 'text-ui-subtext' }}">
                                    <span class="h-2.5 w-2.5 rounded-full {{ $proofRecord ? 'bg-ui-proof' : 'bg-slate-300' }}"></span>
                                    {{ $proofRecord ? 'Proof recorded' : 'Proof pending' }}
                                </span>
                            </dd>
                        </div>

                        @if($proofHash)
                            <div class="rounded-xl bg-cyan-50 p-3 sm:col-span-2">
                                <dt class="text-xs font-medium uppercase tracking-wide text-cyan-700">Proof Bundle Hash</dt>
                                <dd class="mt-1 break-all font-mono text-xs font-semibold text-cyan-800" title="{{ $proofHash }}">
                                    {{ $proofHash }}
                                </dd>
                            </div>
                        @endif

                        <div class="rounded-xl bg-ui-canvas/70 p-3">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext">Release Timestamp</dt>
                            <dd class="mt-1 font-semibold {{ $settlement->settled_at ? 'text-ui-success' : 'text-ui-subtext' }}">
                                {{ $settlement->settled_at ? $settlement->settled_at->format('M d, Y') : 'Awaiting release' }}
                            </dd>
                            @if($settlement->settled_at)
                                <dd class="mt-1 text-xs text-emerald-600">
                                    {{ $settlement->settled_at->format('g:i A') }}
                                </dd>
                            @endif
                        </div>
                    </dl>

                    <div class="mt-4">
                        @if($settlement->status === 'Pending')
                            <form method="POST"
                                  action="{{ route('admin.settlements.settle', $settlement) }}"
                                  data-confirm
                                  data-confirm-title="Release merchant settlement?"
                                  data-confirm-message="This records the cooperative reimbursement as released and notifies the merchant. No wallet or token transfer will be executed."
                                  data-confirm-button="Release settlement"
                                  data-confirm-tone="success"
                                  data-loading-text="Releasing settlement..."
                                  data-loader-title="Releasing settlement..."
                                  data-loader-message="Updating the reimbursement lifecycle and notifying the merchant.">
                                @csrf

                                <button type="submit"
                                        class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-ui-action px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor sm:w-auto">
                                    Release Settlement
                                </button>
                            </form>
                        @else
                            <span class="inline-flex min-h-10 items-center rounded-xl bg-ui-canvas px-4 py-2 text-sm font-semibold text-ui-subtext">
                                Released {{ $settlement->settled_at?->format('M d, Y') }}
                            </span>
                        @endif
                    </div>
                </article>
            @empty
                <div class="px-4 py-14 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-ui-canvas text-ui-subtext">
                        <x-icon name="credit-card" size="h-8 w-8" />
                    </div>

                    <h3 class="mt-5 text-lg font-semibold text-ui-text">
                        No settlement records found
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm text-ui-subtext">
                        {{ $hasFilters
                            ? 'No settlement records match the selected status. Clear filters to return to the full console.'
                            : 'Settlement records appear after merchants process valid claims.' }}
                    </p>
                </div>
            @endforelse
        </div>

        <x-slot:footer>
            <div class="flex flex-col items-center justify-center gap-3 text-center">
                <p class="text-sm text-ui-subtext">
                    Page {{ $settlements->currentPage() }} of {{ $settlements->lastPage() }}
                </p>

                <div class="flex max-w-full justify-center overflow-x-auto">
                    {{ $settlements->links() }}
                </div>
            </div>
        </x-slot:footer>
    </x-table-card>
</div>

@endsection
