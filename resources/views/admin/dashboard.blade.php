@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')

@php
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
        @foreach([
            [
                'label' => 'Total Approved Assistance',
                'value' => '₱' . number_format($totalApprovedAssistance, 2),
                'sub' => 'Approved assistance value',
                'tone' => 'success',
                'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            [
                'label' => 'Pending Approvals',
                'value' => number_format($pendingRequests),
                'sub' => 'Awaiting admin review',
                'tone' => 'warning',
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            [
                'label' => 'Pending Settlement Value',
                'value' => '₱' . number_format($pendingSettlementAmount, 2),
                'sub' => 'Outstanding merchant reimbursement',
                'tone' => 'danger',
                'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
            ],
            [
                'label' => 'Morph Confirmations',
                'value' => number_format($confirmedBlockchainLogs),
                'sub' => 'Confirmed proof records',
                'tone' => 'proof',
                'icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1',
            ],
        ] as $card)
            @php
                $toneClass = match ($card['tone']) {
                    'success' => 'bg-ui-success/10 text-ui-success ring-ui-success/15',
                    'warning' => 'bg-ui-warning/10 text-ui-warning ring-ui-warning/15',
                    'danger' => 'bg-ui-danger/10 text-ui-danger ring-ui-danger/15',
                    default => 'bg-ui-proof/10 text-ui-proof ring-ui-proof/15',
                };
            @endphp

            <div class="group relative overflow-hidden rounded-2xl border border-ui-border/80 bg-ui-surface/95 p-5 shadow-[0_14px_34px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5 transition duration-200 hover:-translate-y-0.5 hover:shadow-[0_18px_40px_rgba(15,47,44,0.10)]">
                <div class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-ui-action/5 blur-2xl"></div>

                <div class="relative">
                    <div class="mb-4 flex items-start justify-between gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $toneClass }} ring-1">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}" />
                            </svg>
                        </div>

                        <span class="mt-1 h-2 w-2 rounded-full {{ $card['tone'] === 'danger' ? 'bg-ui-danger' : ($card['tone'] === 'warning' ? 'bg-ui-warning' : ($card['tone'] === 'proof' ? 'bg-ui-proof' : 'bg-ui-success')) }}"></span>
                    </div>

                    <p class="text-xs font-semibold uppercase tracking-wider text-ui-subtext/70">
                        {{ $card['label'] }}
                    </p>

                    <p class="mt-2 text-2xl font-bold tracking-tight text-ui-anchor">
                        {{ $card['value'] }}
                    </p>

                    <p class="mt-1 text-sm text-ui-subtext/80">
                        {{ $card['sub'] }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
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

        <div class="rounded-2xl border border-ui-success/20 bg-gradient-to-br from-emerald-50 to-teal-50/70 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.06)] ring-1 ring-ui-success/10">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm text-ui-subtext">
                        Approval Rate
                    </p>

                    <p class="mt-1 text-2xl font-bold text-ui-success">
                        {{ number_format($approvalRate, 1) }}%
                    </p>
                </div>

                <span class="mt-1 h-3 w-3 rounded-full bg-ui-success"></span>
            </div>

            <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-ui-success/15">
                <div class="h-full rounded-full bg-ui-success" style="width: {{ min($approvalRate, 100) }}%"></div>
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
            @foreach([
                ['label' => 'Request', 'class' => 'border border-ui-border/80 bg-ui-surface/85 text-ui-anchor/85 ring-ui-border/70'],
                ['label' => 'Review', 'class' => 'bg-ui-warning/10 text-ui-warning ring-ui-warning/15'],
                ['label' => 'Approved', 'class' => 'bg-ui-success/10 text-ui-success ring-ui-success/15'],
                ['label' => 'QR Issued', 'class' => 'bg-teal-50 text-teal-700 ring-teal-200'],
                ['label' => 'Claimed', 'class' => 'bg-ui-proof/10 text-ui-proof ring-ui-proof/15'],
                ['label' => 'Released', 'class' => 'bg-ui-action/10 text-ui-action ring-ui-action/15'],
            ] as $index => $stage)
                <div class="flex min-w-[7.5rem] flex-1 items-center justify-center rounded-full px-3 py-2 text-center shadow-sm shadow-ui-anchor/5 ring-1 sm:flex-none {{ $stage['class'] }}">
                    <span class="min-w-0 truncate">
                        {{ $stage['label'] }}
                    </span>
                </div>

                @if($index < 5)
                    <x-icon name="chevron-right" size="hidden h-4 w-4 text-ui-action/45 sm:block" />
                @endif
            @endforeach
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
                        <x-status-badge :status="$latestBlockchainTransaction->blockchain_status" :tone="$latestBlockchainTransaction->blockchain_status" size="xs" />

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
        <div class="overflow-hidden rounded-2xl border border-ui-border/80 bg-ui-surface/95 shadow-[0_14px_34px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <div class="flex items-center justify-between gap-4 border-b border-ui-border/70 bg-gradient-to-r from-[#f8fdfb] to-ui-muted/30 px-5 py-4">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-ui-warning/10 text-ui-warning ring-1 ring-ui-warning/15">
                        <x-icon name="list-checks" size="h-4 w-4" />
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-sm font-bold text-ui-anchor">
                            Pending Approvals
                        </h2>

                        <p class="text-xs text-ui-subtext/70">
                            Assistance requests waiting for review.
                        </p>
                    </div>
                </div>

                <a href="{{ route('admin.assistance-requests.index') }}"
                   class="shrink-0 rounded-lg border border-ui-action/20 bg-ui-action/10 px-3 py-1.5 text-xs font-bold text-ui-action transition hover:bg-ui-action hover:text-white">
                    Review
                </a>
            </div>

            <div class="grid grid-cols-[1fr_auto] border-b border-ui-border/50 bg-ui-canvas/60 px-5 py-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-ui-subtext/55">Member / Program</span>
                <span class="text-right text-[10px] font-bold uppercase tracking-wider text-ui-subtext/55">Amount</span>
            </div>

            <div class="divide-y divide-ui-border/60">
                @forelse($latestPendingRequests as $request)
                    <div class="grid grid-cols-[1fr_auto] items-center gap-4 px-5 py-3.5 transition hover:bg-ui-canvas/50">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-ui-warning/10 text-xs font-bold text-ui-warning ring-1 ring-ui-warning/15">
                                {{ strtoupper(substr($request->member->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-ui-anchor">
                                    {{ $request->member->name ?? 'Unknown member' }}
                                </p>

                                <p class="truncate text-xs text-ui-subtext/70">
                                    {{ $request->program->program_name ?? 'Assistance program' }}
                                </p>
                            </div>
                        </div>

                        <p class="shrink-0 text-sm font-bold text-ui-anchor">
                            ₱{{ number_format($request->requested_amount, 2) }}
                        </p>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center">
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

        <div class="overflow-hidden rounded-2xl border border-ui-border/80 bg-ui-surface/95 shadow-[0_14px_34px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <div class="flex items-center justify-between gap-4 border-b border-ui-border/70 bg-gradient-to-r from-[#f8fdfb] to-ui-muted/30 px-5 py-4">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-ui-action/10 text-ui-action ring-1 ring-ui-action/15">
                        <x-icon name="credit-card" size="h-4 w-4" />
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-sm font-bold text-ui-anchor">
                            Pending Settlements
                        </h2>

                        <p class="text-xs text-ui-subtext/70">
                            Merchant reimbursements awaiting completion.
                        </p>
                    </div>
                </div>

                <a href="{{ route('admin.settlements.index') }}"
                   class="shrink-0 rounded-lg border border-ui-action/20 bg-ui-action/10 px-3 py-1.5 text-xs font-bold text-ui-action transition hover:bg-ui-action hover:text-white">
                    Open
                </a>
            </div>

            <div class="grid grid-cols-[1fr_auto] border-b border-ui-border/50 bg-ui-canvas/60 px-5 py-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-ui-subtext/55">Merchant / Reference</span>
                <span class="text-right text-[10px] font-bold uppercase tracking-wider text-ui-subtext/55">Amount</span>
            </div>

            <div class="divide-y divide-ui-border/60">
                @forelse($latestPendingSettlements as $settlement)
                    @php
                        $merchantProfile = $settlement->merchant?->merchantProfile;
                    @endphp

                    <div class="grid grid-cols-[1fr_auto] items-center gap-4 px-5 py-3.5 transition hover:bg-ui-canvas/50">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-ui-action/10 text-xs font-bold text-ui-action ring-1 ring-ui-action/15">
                                {{ strtoupper(substr($merchantProfile->business_name ?? $settlement->merchant->name ?? 'M', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-ui-anchor">
                                    {{ $merchantProfile->business_name ?? $settlement->merchant->name ?? 'Merchant account' }}
                                </p>

                                <p class="truncate font-mono text-xs text-ui-subtext/70">
                                    {{ $settlement->assistanceRequest->reference_code ?? 'No reference' }}
                                </p>
                            </div>
                        </div>

                        <p class="shrink-0 text-sm font-bold text-ui-anchor">
                            ₱{{ number_format($settlement->amount, 2) }}
                        </p>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center">
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
        <div class="overflow-hidden rounded-2xl border border-ui-border/80 bg-ui-surface/95 shadow-[0_14px_34px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5 xl:col-span-3">
            <div class="flex items-center justify-between gap-4 border-b border-ui-border/70 bg-gradient-to-r from-[#f8fdfb] to-ui-muted/30 px-5 py-4">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-ui-action/10 text-ui-action ring-1 ring-ui-action/15">
                        <x-icon name="activity" size="h-4 w-4" />
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-sm font-bold text-ui-anchor">
                            Live Operations Feed
                        </h2>

                        <p class="text-xs text-ui-subtext/70">
                            Recent approvals, claims, settlements, and verification activity.
                        </p>
                    </div>
                </div>

                <a href="{{ route('admin.activity-logs.index') }}"
                   class="shrink-0 rounded-lg border border-ui-action/20 bg-ui-action/10 px-3 py-1.5 text-xs font-bold text-ui-action transition hover:bg-ui-action hover:text-white">
                    View all
                </a>
            </div>

            <div class="grid grid-cols-[auto_1fr_auto] gap-3 border-b border-ui-border/50 bg-ui-canvas/60 px-5 py-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-ui-subtext/55">Type</span>
                <span class="text-[10px] font-bold uppercase tracking-wider text-ui-subtext/55">Activity</span>
                <span class="text-right text-[10px] font-bold uppercase tracking-wider text-ui-subtext/55">Status / Time</span>
            </div>

            <div class="divide-y divide-ui-border/60">
                @forelse($recentActivities as $activity)
                    <div class="grid grid-cols-[auto_1fr_auto] items-start gap-3 px-5 py-3.5 transition hover:bg-ui-canvas/50">
                        <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg {{ in_array($activity->status, ['Rejected', 'Failed'], true) ? 'bg-ui-danger/10 text-ui-danger ring-1 ring-ui-danger/15' : (in_array($activity->status, ['Approved', 'Confirmed', 'Released', 'Settled'], true) ? 'bg-ui-success/10 text-ui-success ring-1 ring-ui-success/15' : 'bg-ui-warning/10 text-ui-warning ring-1 ring-ui-warning/15') }}">
                                <x-icon :name="$activity->status === 'Rejected' || $activity->status === 'Failed' ? 'x-circle' : 'check-circle'" size="h-5 w-5" />
                        </div>

                        <div class="min-w-0">
                            <p class="text-sm font-semibold leading-tight text-ui-anchor">
                                {{ $activity->title }}
                            </p>

                            <p class="mt-0.5 text-xs leading-relaxed text-ui-subtext/60">
                                {{ \Illuminate\Support\Str::limit($activity->description ?? 'No additional details.', 90) }}
                            </p>

                            <p class="mt-1 text-[10px] text-ui-subtext/45">
                                By {{ $activity->user->name ?? 'System' }}
                            </p>
                        </div>

                        <div class="shrink-0 text-right">
                            @if($activity->status)
                                <x-status-badge :status="$activity->status" :tone="$activity->status" size="xs" />
                            @endif

                            <p class="mt-1 text-[10px] text-ui-subtext/45">
                                {{ $activity->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center">
                        <p class="text-ui-subtext/70">
                            No operational activity recorded yet.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-ui-border/80 bg-ui-surface/95 shadow-[0_14px_34px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5 xl:col-span-2">
            <div class="flex items-center gap-3 border-b border-ui-border/70 bg-gradient-to-r from-[#f8fdfb] to-ui-muted/30 px-5 py-4">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-ui-proof/10 text-ui-proof ring-1 ring-ui-proof/15">
                    <x-icon name="file-text" size="h-4 w-4" />
                </div>
                <div class="min-w-0">
                    <h2 class="text-sm font-bold text-ui-anchor">
                        Recent Assistance Activity
                    </h2>

                    <p class="text-xs text-ui-subtext/70">
                        Latest requests, approvals, claims, and assistance values.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-[1fr_auto] border-b border-ui-border/50 bg-ui-canvas/60 px-5 py-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-ui-subtext/55">Member / Program</span>
                <span class="text-right text-[10px] font-bold uppercase tracking-wider text-ui-subtext/55">Status / Amount</span>
            </div>

            <div class="divide-y divide-ui-border/60">
                @forelse($recentRequests as $request)
                    <div class="grid grid-cols-[1fr_auto] items-center gap-3 px-5 py-3.5 transition hover:bg-ui-canvas/50">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-ui-action/10 text-xs font-bold text-ui-action ring-1 ring-ui-action/15">
                                {{ strtoupper(substr($request->member->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-ui-anchor">
                                    {{ $request->member->name ?? 'Unknown member' }}
                                </p>

                                <p class="truncate text-xs text-ui-subtext/70">
                                    {{ $request->program->program_name ?? 'Assistance program' }}
                                </p>

                                <p class="font-mono text-[10px] text-ui-subtext/45">
                                    {{ $request->reference_code ?? 'Pending reference' }}
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0 text-right">
                            <x-status-badge
                                :status="$request->is_claimed ? 'Claimed' : $request->status"
                                :tone="$request->is_claimed ? 'claimed' : $request->status"
                                size="xs" />

                            <p class="mt-1 text-sm font-bold text-ui-anchor">
                                ₱{{ number_format($request->approved_amount ?? $request->requested_amount, 2) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center">
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
