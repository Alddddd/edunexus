@extends('layouts.dashboard')

@section('title', 'Merchant Dashboard')

@section('content')

@php
    $profileStatus = $merchantProfile->status ?? 'No profile';
    $settlementTone = fn ($status) => in_array($status, ['Released', 'Settled'], true) ? 'settled' : ($status === 'Partially Released' ? 'proof' : 'pending');
    $settlementLabel = fn ($status) => match ($status) {
        'Released', 'Settled' => html_entity_decode('&#8369; payout released'),
        'Partially Released' => html_entity_decode('Partial &#8369; payout released'),
        default => 'Pending',
    };
    $payoutComplete = filled($merchantProfile?->payout_account_name) && filled($merchantProfile?->payout_account_number);
@endphp

<div class="w-full min-w-0 max-w-7xl space-y-5 text-ui-anchor">
    <section class="rounded-2xl border border-ui-border/80 bg-gradient-to-br from-ui-surface via-ui-surface/90 to-ui-proof/10 p-5 shadow-[0_22px_52px_rgba(15,47,44,0.10)] ring-1 ring-ui-anchor/5">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold uppercase tracking-wider text-ui-action">
                    Merchant Claim Terminal
                </p>

                <h1 class="mt-2 text-3xl font-bold text-ui-anchor">
                    Merchant Dashboard
                </h1>

                <p class="mt-2 max-w-3xl leading-6 text-ui-subtext/90">
                    Validate member claim passes, process eligible assistance claims, and monitor cooperative settlement status.
                </p>
            </div>

            <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
                <a href="{{ route('merchant.payout-settings.edit') }}"
                   class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-ui-border bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas sm:w-auto">
                    Payout Settings
                </a>

                <a href="{{ route('merchant.settlements.index') }}"
                   class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-ui-border bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas sm:w-auto">
                    Settlements
                </a>

                <a href="{{ route('merchant.claims.index') }}"
                   class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-ui-action px-5 py-2.5 text-sm font-semibold text-white shadow-[0_10px_20px_rgba(11,93,86,0.18)] transition hover:bg-ui-anchor sm:w-auto">
                    Validate Claim
                </a>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-warning bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-ui-subtext">Pending Validations</p>
            <p class="mt-2 text-3xl font-bold text-ui-warning">{{ number_format($pendingValidationCount) }}</p>
            <p class="mt-1 text-sm text-amber-600">Approved passes in your category</p>
        </div>

        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-proof bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-ui-subtext">Processed Claims</p>
            <p class="mt-2 text-3xl font-bold text-ui-text">{{ number_format($processedClaims) }}</p>
            <p class="mt-1 text-sm text-cyan-600">Validated claim transactions</p>
        </div>

        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-warning bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-ui-subtext">Pending Settlements</p>
            <p class="mt-2 text-3xl font-bold text-ui-warning">{{ number_format($pendingSettlements) }}</p>
            <p class="mt-1 text-sm text-amber-600">Awaiting cooperative reimbursement</p>
        </div>

        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-proof bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-ui-subtext">Morph Proofs</p>
            <p class="mt-2 text-3xl font-bold text-ui-proof">{{ number_format($morphProofConfirmations) }}</p>
            <p class="mt-1 text-sm text-cyan-600">Confirmed claim proofs</p>
        </div>

        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-success bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-ui-subtext">Settlement Value</p>
            <p class="mt-2 text-3xl font-bold text-ui-text">&#8369;{{ number_format($totalSettlementValue, 2) }}</p>
            <p class="mt-1 text-sm text-teal-600">Total merchant claim value</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <x-form-card title="Merchant Profile" description="Accreditation details used during programmable claim validation.">
            <x-slot:actions>
                <x-status-badge :status="$profileStatus" :tone="$profileStatus === 'Active' ? 'active' : 'danger'" />
            </x-slot:actions>

            <div class="space-y-4">
                <div class="rounded-xl bg-ui-canvas/70 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-ui-subtext">Business</p>
                    <p class="mt-2 font-semibold text-ui-text">{{ $merchantProfile->business_name ?? auth()->user()->name }}</p>
                </div>

                <div class="rounded-xl bg-ui-canvas/70 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-ui-subtext">Merchant Category</p>
                    <p class="mt-2 font-semibold text-ui-text">{{ $merchantProfile->merchant_category ?? 'Not configured' }}</p>
                </div>

                <div class="rounded-xl bg-ui-canvas/70 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-ui-subtext">Pending Value</p>
                    <p class="mt-2 font-semibold text-ui-text">&#8369;{{ number_format($pendingSettlementValue, 2) }}</p>
                </div>

                <div class="rounded-xl bg-ui-canvas/70 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-ui-subtext">GCash Payout</p>
                    <div class="mt-2">
                        <x-status-badge
                            :status="$payoutComplete ? 'Complete' : 'Missing payout details'"
                            :tone="$payoutComplete ? 'success' : 'warning'"
                            size="xs" />
                    </div>
                    <p class="mt-3 font-semibold text-ui-text">{{ $merchantProfile->payout_account_name ?? 'Not configured' }}</p>
                    <p class="mt-1 font-mono text-sm text-ui-subtext">{{ $merchantProfile->payout_account_number ?? 'Awaiting setup' }}</p>
                    <a href="{{ route('merchant.payout-settings.edit') }}"
                       class="mt-3 inline-flex min-h-10 items-center justify-center rounded-xl bg-ui-action px-4 py-2 text-xs font-semibold text-white transition hover:bg-ui-anchor">
                        Edit Payout Settings
                    </a>
                </div>
            </div>
        </x-form-card>

        <section class="demo-surface-card rounded-2xl border border-ui-action/15 bg-gradient-to-br from-ui-action/10 via-ui-surface/90 to-ui-proof/10 p-4 shadow-[0_16px_32px_rgba(11,93,86,0.08)] ring-1 ring-ui-anchor/5 sm:p-5 xl:col-span-2">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-teal-700">
                        Claim Processing Workflow
                    </p>

                    <h2 class="mt-1 text-lg font-bold text-ui-text">
                        Validate, Process, Record Proof, Settle
                    </h2>

                    <p class="mt-1 max-w-3xl text-sm leading-6 text-ui-subtext/90">
                        EduNexUs checks approval status, expiration, duplicate use, amount limits, and merchant category before processing a claim and recording Morph proof.
                    </p>
                </div>

                <div class="flex justify-start lg:justify-end">
                    <x-status-badge status="Rule-enforced terminal" tone="proof" />
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-2 text-sm md:grid-cols-4">
                <div class="rounded-xl border border-ui-border/70 bg-ui-surface/80 p-3 shadow-sm shadow-ui-anchor/5">
                    <p class="font-semibold text-ui-text">1. Reference</p>
                    <p class="mt-1 text-xs leading-5 text-ui-subtext">Scan QR or type claim code.</p>
                </div>

                <div class="rounded-xl border border-ui-border/70 bg-ui-surface/80 p-3 shadow-sm shadow-ui-anchor/5">
                    <p class="font-semibold text-ui-text">2. Rules</p>
                    <p class="mt-1 text-xs leading-5 text-ui-subtext">Program and merchant checks run.</p>
                </div>

                <div class="rounded-xl border border-ui-border/70 bg-ui-surface/80 p-3 shadow-sm shadow-ui-anchor/5">
                    <p class="font-semibold text-ui-text">3. Process</p>
                    <p class="mt-1 text-xs leading-5 text-ui-subtext">Mark used and create settlement.</p>
                </div>

                <div class="rounded-xl border border-ui-border/70 bg-ui-surface/80 p-3 shadow-sm shadow-ui-anchor/5">
                    <p class="font-semibold text-ui-text">4. Proof</p>
                    <p class="mt-1 text-xs leading-5 text-ui-subtext">Record tamper-resistant proof.</p>
                </div>
            </div>
        </section>
    </div>

    <x-table-card
        title="Pending Claim Validations"
        description="Approved, unclaimed assistance passes that match this merchant profile category.">
        <x-slot:actions>
            <a href="{{ route('merchant.claims.index') }}"
               class="text-sm font-semibold text-ui-action hover:text-ui-anchor">
                Validate by reference
            </a>
        </x-slot:actions>

        <div class="divide-y divide-ui-border/80">
            @forelse($pendingValidations as $claim)
                <div class="px-5 py-4 transition hover:bg-ui-canvas/70">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="font-semibold text-ui-text">{{ $claim->member->name ?? 'Member' }}</p>
                            <p class="mt-1 text-sm text-ui-subtext">{{ $claim->program->program_name ?? 'Assistance program' }}</p>
                            <p class="mt-2 font-mono text-xs text-ui-subtext">{{ $claim->reference_code ?? 'Reference pending' }}</p>
                        </div>

                        <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:flex-wrap sm:items-center md:justify-end">
                            <x-status-badge status="Ready for validation" tone="warning" />
                            <p class="font-semibold text-ui-text">&#8369;{{ number_format($claim->approved_amount, 2) }}</p>

                            @if($claim->reference_code)
                                <a href="{{ route('merchant.claims.verify', ['reference_code' => $claim->reference_code]) }}"
                                   class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-ui-action px-4 py-2 text-xs font-semibold text-white transition hover:bg-ui-anchor sm:w-auto">
                                    Verify
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-10 text-center">
                    <p class="font-semibold text-ui-text">No pending validations</p>
                    <p class="mt-2 text-sm text-ui-subtext">
                        Approved claim passes for this merchant category will appear here before processing.
                    </p>
                </div>
            @endforelse
        </div>
    </x-table-card>

    <x-table-card
        title="Recent Processed Claims"
        description="Claims processed by this merchant and their reimbursement status.">
        <x-slot:actions>
            <a href="{{ route('merchant.settlements.index') }}"
               class="text-sm font-semibold text-ui-action hover:text-ui-anchor">
                View all settlements
            </a>
        </x-slot:actions>

        <div class="divide-y divide-ui-border/80">
            @forelse($recentSettlements as $settlement)
                <div class="px-5 py-4 transition hover:bg-ui-canvas/70">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="font-semibold text-ui-text">{{ $settlement->assistanceRequest->member->name ?? 'Member' }}</p>
                            <p class="mt-1 text-sm text-ui-subtext">{{ $settlement->assistanceRequest->program->program_name ?? 'Assistance program' }}</p>
                            <p class="mt-2 font-mono text-xs text-ui-subtext">{{ $settlement->assistanceRequest->reference_code ?? 'No reference' }}</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 md:justify-end">
                            <x-status-badge :status="$settlementLabel($settlement->status)" :tone="$settlementTone($settlement->status)" />
                            <p class="font-semibold text-ui-text">&#8369;{{ number_format($settlement->amount, 2) }}</p>
                            @if((float) $settlement->total_released > 0)
                                <p class="text-xs text-emerald-600">Released: &#8369;{{ number_format((float) $settlement->total_released, 2) }}</p>
                            @endif
                            @if((float) $settlement->remaining_balance > 0)
                                <p class="text-xs text-amber-600">Remaining: &#8369;{{ number_format((float) $settlement->remaining_balance, 2) }}</p>
                            @endif
                            <p class="text-xs text-ui-subtext">{{ $settlement->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-14 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-ui-canvas text-ui-subtext">
                        <x-icon name="store" size="h-8 w-8" />
                    </div>

                    <h3 class="mt-5 text-lg font-semibold text-ui-text">
                        No processed claims yet
                    </h3>

                    <p class="mt-2 text-sm text-ui-subtext">
                        Validated member claims will appear here with settlement tracking.
                    </p>

                    <a href="{{ route('merchant.claims.index') }}"
                       class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-ui-action px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-ui-anchor sm:w-auto">
                        Validate Claim
                    </a>
                </div>
            @endforelse
        </div>
    </x-table-card>
</div>

@endsection
