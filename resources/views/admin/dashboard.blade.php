@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')

@php
    $statusClasses = function ($status) {
        return match ($status) {
            'Approved', 'Confirmed', 'Settled' => 'bg-ui-success/10 text-ui-success ring-1 ring-ui-success/15',
            'Rejected', 'Failed' => 'bg-ui-danger/10 text-ui-danger ring-1 ring-ui-danger/15',
            default => 'bg-ui-warning/10 text-ui-warning ring-1 ring-ui-warning/15',
        };
    };

    $latestHash = $latestBlockchainTransaction?->transaction_hash;
    $shortLatestHash = $latestHash && str_starts_with($latestHash, '0x')
        ? substr($latestHash, 0, 10) . '...' . substr($latestHash, -8)
        : ($latestHash ?? 'No hash recorded');
@endphp

<div class="w-full min-w-0 max-w-7xl space-y-6 text-ui-anchor">
    <x-page-header
        title="EduNexUs Admin Dashboard"
        eyebrow="Operational Command Center"
        description="Monitor assistance approvals, merchant claims, settlements, Morph proof records, and live operational activity.">
        <x-slot:actions>
            <a href="{{ route('admin.assistance-requests.index') }}"
               class="inline-flex items-center justify-center rounded-xl bg-ui-action px-5 py-3 text-sm font-semibold text-white shadow-[0_10px_20px_rgba(11,93,86,0.18)] transition hover:bg-primary-dark hover:shadow-[0_14px_28px_rgba(11,93,86,0.22)]">
                Review Requests
            </a>

            <a href="{{ route('admin.settlements.index') }}"
               class="inline-flex items-center justify-center rounded-xl border border-ui-border/80 bg-ui-surface/70 px-5 py-3 text-sm font-semibold text-ui-anchor/85 shadow-sm shadow-ui-anchor/5 transition hover:border-ui-action/25 hover:bg-ui-surface">
                Settlements
            </a>

            <a href="{{ route('admin.blockchain-transactions.index') }}"
               class="inline-flex items-center justify-center rounded-xl border border-ui-border/80 bg-ui-surface/70 px-5 py-3 text-sm font-semibold text-ui-anchor/85 shadow-sm shadow-ui-anchor/5 transition hover:border-ui-action/25 hover:bg-ui-surface">
                Morph Proofs
            </a>

            <a href="{{ route('admin.activity-logs.index') }}"
               class="inline-flex items-center justify-center rounded-xl border border-ui-border/80 bg-ui-surface/70 px-5 py-3 text-sm font-semibold text-ui-anchor/85 shadow-sm shadow-ui-anchor/5 transition hover:border-ui-action/25 hover:bg-ui-surface">
                Activity Timeline
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-success bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5 transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_20px_44px_rgba(15,47,44,0.10)]">
            <p class="text-sm text-ui-subtext">
                Total Approved Assistance
            </p>

            <p class="mt-3 text-3xl font-bold text-ui-anchor">
                ₱{{ number_format($totalApprovedAssistance, 2) }}
            </p>

            <p class="mt-2 text-sm text-ui-subtext/85">
                Approved assistance value
            </p>
        </div>

        <div class="rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-warning bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5 transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_20px_44px_rgba(15,47,44,0.10)]">
            <p class="text-sm text-ui-subtext">
                Pending Approvals
            </p>

            <p class="mt-3 text-3xl font-bold text-ui-anchor">
                {{ number_format($pendingRequests) }}
            </p>

            <p class="mt-2 text-sm text-ui-subtext/85">
                Awaiting admin review
            </p>
        </div>

        <div class="rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-warning bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5 transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_20px_44px_rgba(15,47,44,0.10)]">
            <p class="text-sm text-ui-subtext">
                Pending Settlement Value
            </p>

            <p class="mt-3 text-3xl font-bold text-ui-anchor">
                ₱{{ number_format($pendingSettlementAmount, 2) }}
            </p>

            <p class="mt-2 text-sm text-ui-subtext/85">
                Outstanding merchant reimbursement
            </p>
        </div>

        <div class="rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-proof bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5 transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_20px_44px_rgba(15,47,44,0.10)]">
            <p class="text-sm text-ui-subtext">
                Morph Confirmations
            </p>

            <p class="mt-3 text-3xl font-bold text-ui-anchor">
                {{ number_format($confirmedBlockchainLogs) }}
            </p>

            <p class="mt-2 text-sm text-ui-subtext/85">
                Confirmed proof records
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-ui-border/80 bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-ui-subtext">
                        Assistance Engine
                    </p>

                    <p class="mt-1 text-lg font-semibold text-ui-success">
                        Operational
                    </p>
                </div>

                <span class="h-3 w-3 rounded-full bg-ui-success"></span>
            </div>
        </div>

        <div class="rounded-2xl border border-ui-border/80 bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-ui-subtext">
                        Merchant Validation
                    </p>

                    <p class="mt-1 text-lg font-semibold text-ui-proof">
                        Active
                    </p>
                </div>

                <span class="h-3 w-3 rounded-full bg-ui-proof"></span>
            </div>
        </div>

        <div class="rounded-2xl border border-ui-border/80 bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-ui-subtext">
                        Morph Verification
                    </p>

                    <p class="mt-1 text-lg font-semibold text-ui-action">
                        Connected
                    </p>
                </div>

                <span class="h-3 w-3 rounded-full bg-ui-action"></span>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-ui-action/15 bg-gradient-to-br from-ui-action/10 via-ui-surface/90 to-ui-proof/10 p-6 shadow-[0_20px_44px_rgba(11,93,86,0.10)] ring-1 ring-ui-anchor/5">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-ui-action">
                    Live MVP Workflow
                </p>

                <h2 class="mt-2 text-2xl font-bold text-ui-anchor">
                    Programmable Assistance with Morph Proof Recording
                </h2>

                <p class="mt-2 max-w-3xl leading-6 text-ui-subtext/90">
                    EduNexUs validates merchant claims through programmable rules, records proof on Morph, and tracks settlement status for cooperative reimbursement.
                </p>
            </div>

            <span class="inline-flex w-fit rounded-xl border border-ui-action/20 bg-ui-surface/80 px-4 py-2 text-sm font-semibold text-ui-action shadow-sm shadow-ui-anchor/5 ring-1 ring-ui-action/10">
                Demo-ready workflow
            </span>
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-2 text-sm font-semibold">
            <div class="flex min-w-[8rem] flex-1 items-center gap-2 rounded-full border border-ui-border/80 bg-ui-surface/85 px-3 py-2 text-ui-anchor/85 shadow-sm shadow-ui-anchor/5 ring-1 ring-ui-border/70 sm:flex-none">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-ui-muted text-xs text-ui-anchor/75">1</span>
                <span class="min-w-0 truncate">Request</span>
            </div>

            <x-icon name="chevron-right" size="hidden h-4 w-4 text-ui-action/45 sm:block" />

            <div class="flex min-w-[8rem] flex-1 items-center gap-2 rounded-full bg-ui-warning/10 px-3 py-2 text-ui-warning ring-1 ring-ui-warning/15 sm:flex-none">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-ui-warning/15 text-xs">2</span>
                <span class="min-w-0 truncate">Approval</span>
            </div>

            <x-icon name="chevron-right" size="hidden h-4 w-4 text-ui-action/45 sm:block" />

            <div class="flex min-w-[8rem] flex-1 items-center gap-2 rounded-full bg-ui-success/10 px-3 py-2 text-ui-success ring-1 ring-ui-success/15 sm:flex-none">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-ui-success/15 text-xs">3</span>
                <span class="min-w-0 truncate">QR Generated</span>
            </div>

            <x-icon name="chevron-right" size="hidden h-4 w-4 text-ui-action/45 sm:block" />

            <div class="flex min-w-[10rem] flex-1 items-center gap-2 rounded-full bg-ui-proof/10 px-3 py-2 text-ui-proof ring-1 ring-ui-proof/15 sm:flex-none">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-ui-proof/15 text-xs">4</span>
                <span class="min-w-0 truncate">Merchant Validation</span>
            </div>

            <x-icon name="chevron-right" size="hidden h-4 w-4 text-ui-action/45 sm:block" />

            <div class="flex min-w-[8rem] flex-1 items-center gap-2 rounded-full bg-ui-action/10 px-3 py-2 text-ui-action ring-1 ring-ui-action/15 sm:flex-none">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-ui-action/15 text-xs">5</span>
                <span class="min-w-0 truncate">Morph Proof</span>
            </div>

            <x-icon name="chevron-right" size="hidden h-4 w-4 text-ui-action/45 sm:block" />

            <div class="flex min-w-[8rem] flex-1 items-center gap-2 rounded-full bg-ui-surface/85 px-3 py-2 text-ui-anchor/85 shadow-sm shadow-ui-anchor/5 ring-1 ring-ui-border/70 sm:flex-none">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-ui-muted text-xs text-ui-anchor/75">6</span>
                <span class="min-w-0 truncate">Settlement</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-2xl border border-ui-border/80 bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5 xl:col-span-2">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-ui-anchor">
                        Operational Summary
                    </h2>

                    <p class="mt-1 text-sm text-ui-subtext">
                        Read-only snapshot of approval, settlement, and verification health.
                    </p>
                </div>

                <span class="inline-flex w-fit rounded-full border border-ui-action/20 bg-ui-action/10 px-3 py-1 text-xs font-semibold text-ui-action shadow-sm shadow-ui-anchor/5">
                    {{ number_format($approvalRate, 1) }}% approval rate
                </span>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-2xl border border-ui-border/70 bg-ui-surface/70 p-4 shadow-inner shadow-white/70">
                    <p class="text-xs font-semibold uppercase tracking-wider text-ui-subtext/70">
                        Requests
                    </p>

                    <p class="mt-2 text-2xl font-bold text-ui-anchor">
                        {{ number_format($totalRequests) }}
                    </p>

                    <p class="mt-1 text-sm text-ui-subtext">
                        {{ number_format($approvedRequests) }} approved, {{ number_format($rejectedRequests) }} rejected
                    </p>
                </div>

                <div class="rounded-2xl border border-ui-border/70 bg-ui-surface/70 p-4 shadow-inner shadow-white/70">
                    <p class="text-xs font-semibold uppercase tracking-wider text-ui-subtext/70">
                        Claims
                    </p>

                    <p class="mt-2 text-2xl font-bold text-ui-anchor">
                        {{ number_format($claimedRequests) }}
                    </p>

                    <p class="mt-1 text-sm text-ui-proof">
                        Merchant processed assistance
                    </p>
                </div>

                <div class="rounded-2xl border border-ui-border/70 bg-ui-surface/70 p-4 shadow-inner shadow-white/70">
                    <p class="text-xs font-semibold uppercase tracking-wider text-ui-subtext/70">
                        Settlements
                    </p>

                    <p class="mt-2 text-2xl font-bold text-ui-anchor">
                        {{ number_format($totalSettlements) }}
                    </p>

                    <p class="mt-1 text-sm text-ui-success">
                        ₱{{ number_format($settledAmount, 2) }} settled
                    </p>
                </div>

                <div class="rounded-2xl border border-ui-border/70 bg-ui-surface/70 p-4 shadow-inner shadow-white/70">
                    <p class="text-xs font-semibold uppercase tracking-wider text-ui-subtext/70">
                        Pending Proofs
                    </p>

                    <p class="mt-2 text-2xl font-bold text-ui-anchor">
                        {{ number_format($pendingBlockchainProofs) }}
                    </p>

                    <p class="mt-1 text-sm text-ui-warning">
                        Awaiting verification confirmation
                    </p>
                </div>

                <div class="rounded-2xl border border-ui-border/70 bg-ui-surface/70 p-4 shadow-inner shadow-white/70 md:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-ui-subtext/70">
                        Top Assistance Program
                    </p>

                    <p class="mt-2 text-xl font-bold text-ui-anchor">
                        {{ $topProgramName }}
                    </p>

                    <p class="mt-1 text-sm text-ui-action">
                        Highest request activity
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-ui-border/80 bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-ui-anchor">
                        Latest Morph Proof
                    </h2>

                    <p class="mt-1 text-sm text-ui-subtext">
                        Most recent blockchain verification record.
                    </p>
                </div>
            </div>

            @if($latestBlockchainTransaction)
                <div class="mt-5 space-y-4">
                    <div>
                        <p class="text-sm text-ui-subtext">
                            {{ $latestBlockchainTransaction->transaction_type }} proof
                        </p>

                        <p class="mt-1 font-mono text-xs font-semibold text-ui-text/85">
                            {{ $latestBlockchainTransaction->reference_code ?? 'N/A' }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-ui-border/80 bg-ui-surface/75 px-3 py-2 shadow-inner shadow-white/70">
                        <p class="font-mono text-xs font-semibold text-ui-text/85" title="{{ $latestHash ?? 'No hash recorded' }}">
                            {{ $shortLatestHash }}
                        </p>

                        <p class="mt-1 text-xs text-ui-subtext/70">
                            Transaction hash
                        </p>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses($latestBlockchainTransaction->blockchain_status) }}">
                            {{ $latestBlockchainTransaction->blockchain_status }}
                        </span>

                        <p class="text-xs text-ui-subtext/70">
                            {{ $latestBlockchainTransaction->recorded_at?->diffForHumans() ?? $latestBlockchainTransaction->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <a href="{{ route('admin.blockchain-transactions.index') }}"
                       class="inline-flex w-full items-center justify-center rounded-xl bg-ui-proof px-4 py-2 text-sm font-semibold text-white transition hover:bg-ui-proof/90">
                        Open Verification Console
                    </a>
                </div>
            @else
                <div class="mt-8 rounded-2xl border border-ui-border/70 bg-ui-surface/70 p-6 text-center shadow-inner shadow-white/70">
                    <p class="font-semibold text-ui-text/85">
                        No Morph proof yet
                    </p>

                    <p class="mt-2 text-sm text-ui-subtext">
                        Proof records appear after merchant claim processing.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="overflow-hidden rounded-2xl border border-ui-border/80 bg-ui-surface/95 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <div class="border-b border-ui-border/70 bg-ui-surface/55 px-6 py-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-ui-anchor">
                            Pending Approvals
                        </h2>

                        <p class="mt-1 text-sm text-ui-subtext">
                            Assistance requests waiting for review.
                        </p>
                    </div>

                    <a href="{{ route('admin.assistance-requests.index') }}"
                       class="text-sm font-semibold text-ui-action hover:text-primary-dark">
                        Review
                    </a>
                </div>
            </div>

            <div class="divide-y divide-ui-border/70">
                @forelse($latestPendingRequests as $request)
                    <div class="px-6 py-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-semibold text-ui-anchor">
                                    {{ $request->member->name ?? 'Unknown member' }}
                                </p>

                                <p class="mt-1 text-sm text-ui-subtext">
                                    {{ $request->program->program_name ?? 'Assistance program' }}
                                </p>
                            </div>

                            <p class="shrink-0 font-semibold text-ui-anchor">
                                ₱{{ number_format($request->requested_amount, 2) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center">
                        <p class="font-semibold text-ui-text/85">
                            No pending approvals
                        </p>

                        <p class="mt-1 text-sm text-ui-subtext">
                            The approval queue is currently clear.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-ui-border/80 bg-ui-surface/95 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <div class="border-b border-ui-border/70 bg-ui-surface/55 px-6 py-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-ui-anchor">
                            Pending Settlements
                        </h2>

                        <p class="mt-1 text-sm text-ui-subtext">
                            Merchant reimbursements awaiting completion.
                        </p>
                    </div>

                    <a href="{{ route('admin.settlements.index') }}"
                       class="text-sm font-semibold text-ui-action hover:text-primary-dark">
                        Open
                    </a>
                </div>
            </div>

            <div class="divide-y divide-ui-border/70">
                @forelse($latestPendingSettlements as $settlement)
                    @php
                        $merchantProfile = $settlement->merchant?->merchantProfile;
                    @endphp

                    <div class="px-6 py-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-semibold text-ui-anchor">
                                    {{ $merchantProfile->business_name ?? $settlement->merchant->name ?? 'Merchant account' }}
                                </p>

                                <p class="mt-1 text-sm text-ui-subtext">
                                    {{ $settlement->assistanceRequest->reference_code ?? 'No reference' }}
                                </p>
                            </div>

                            <p class="shrink-0 font-semibold text-ui-anchor">
                                ₱{{ number_format($settlement->amount, 2) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center">
                        <p class="font-semibold text-ui-text/85">
                            No pending settlements
                        </p>

                        <p class="mt-1 text-sm text-ui-subtext">
                            Merchant reimbursement queue is currently clear.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-5">
        <div class="overflow-hidden rounded-2xl border border-ui-border/80 bg-ui-surface/95 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5 xl:col-span-3">
            <div class="border-b border-ui-border/70 bg-ui-surface/55 px-6 py-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-ui-anchor">
                            Live Operations Feed
                        </h2>

                        <p class="mt-1 text-sm text-ui-subtext">
                            Recent approvals, claims, settlements, and verification activity across EduNexUs.
                        </p>
                    </div>

                    <a href="{{ route('admin.activity-logs.index') }}"
                       class="text-sm font-semibold text-ui-action hover:text-primary-dark">
                        View all
                    </a>
                </div>
            </div>

            <div class="divide-y divide-ui-border/70">
                @forelse($recentActivities as $activity)
                    <div class="px-6 py-4 transition hover:bg-ui-surface/75">
                        <div class="flex items-start gap-4">
                            <div class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $statusClasses($activity->status) }}">
                                <x-icon :name="$activity->status === 'Rejected' || $activity->status === 'Failed' ? 'x-circle' : 'check-circle'" size="h-5 w-5" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                    <div>
                                        <p class="font-semibold text-ui-anchor">
                                            {{ $activity->title }}
                                        </p>

                                        <p class="mt-1 text-sm text-ui-subtext">
                                            {{ $activity->description ?? 'No additional details.' }}
                                        </p>

                                        <p class="mt-2 text-xs text-ui-subtext/70">
                                            By {{ $activity->user->name ?? 'System' }}
                                        </p>
                                    </div>

                                    <div class="shrink-0 md:text-right">
                                        @if($activity->status)
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses($activity->status) }}">
                                                {{ $activity->status }}
                                            </span>
                                        @endif

                                        <p class="mt-2 text-xs text-ui-subtext/70">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center">
                        <p class="text-ui-subtext/70">
                            No operational activity recorded yet.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-ui-border/80 bg-ui-surface/95 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5 xl:col-span-2">
            <div class="border-b border-ui-border/70 bg-ui-surface/55 px-6 py-5">
                <h2 class="text-lg font-semibold text-ui-anchor">
                    Recent Assistance Activity
                </h2>

                <p class="mt-1 text-sm text-ui-subtext">
                    Latest requests, approvals, claims, and assistance values.
                </p>
            </div>

            <div class="divide-y divide-ui-border/70">
                @forelse($recentRequests as $request)
                    <div class="px-6 py-4 transition hover:bg-ui-surface/75">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-semibold text-ui-anchor">
                                    {{ $request->member->name ?? 'Unknown member' }}
                                </p>

                                <p class="mt-1 text-sm text-ui-subtext">
                                    {{ $request->program->program_name ?? 'Assistance program' }}
                                </p>

                                <p class="mt-2 font-mono text-xs text-ui-subtext/70">
                                    {{ $request->reference_code ?? 'Pending reference' }}
                                </p>
                            </div>

                            <div class="shrink-0 text-right">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                    {{ $request->is_claimed
                                        ? 'bg-ui-proof/10 text-ui-proof ring-1 ring-ui-proof/15'
                                        : $statusClasses($request->status) }}">
                                    {{ $request->is_claimed ? 'Claimed' : $request->status }}
                                </span>

                                <p class="mt-2 text-sm font-semibold text-ui-anchor">
                                    ₱{{ number_format($request->approved_amount ?? $request->requested_amount, 2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center">
                        <p class="text-ui-subtext/70">
                            No assistance activity yet.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection
