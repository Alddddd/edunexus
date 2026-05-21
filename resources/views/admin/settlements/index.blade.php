@extends('layouts.dashboard')

@section('title', 'Merchant Settlements')

@section('content')

@php
    $hasFilters = filled($filters['status'] ?? null) || filled($filters['search'] ?? null);

    $statusLabel = fn ($status) => match ($status) {
        'Pending' => 'Ready for Release',
        'Partially Released' => 'Partially Released',
        'Released', 'Settled' => 'Released',
        default => $status,
    };

    $statusTone = fn ($status) => match ($status) {
        'Released', 'Settled' => 'settled',
        'Partially Released' => 'proof',
        'Disputed' => 'danger',
        default => 'warning',
    };

    $proofPayload = fn ($proof) => $proof ? (json_decode($proof->payload ?: '[]', true) ?: []) : [];

    $payoutQrUrl = function (?string $path) {
        if (blank($path)) {
            return null;
        }

        return str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
            ? $path
            : asset('storage/' . $path);
    };

    $demoSafeNotice = 'Demo-safe payout layer: PHP/GCash disbursement is simulated to avoid requiring paid payout APIs or real-money transfers during judging. When enabled, a real EDUX ERC-20 testnet transfer is recorded as settlement proof on Morph.';
    $eduxLabel = fn (?array $metadata) => match ($metadata['edux_transfer_status'] ?? 'skipped') {
        'success' => 'Real ERC-20 testnet transfer',
        'failed' => 'EDUX transfer failed',
        default => 'EDUX transfer skipped/not configured',
    };
    $eduxTone = fn (?array $metadata) => match ($metadata['edux_transfer_status'] ?? 'skipped') {
        'success' => 'success',
        'failed' => 'danger',
        default => 'neutral',
    };
@endphp

<div class="w-full min-w-0 max-w-7xl space-y-5">
    <x-page-header
        title="Merchant Settlement Console"
        eyebrow="Settlement Operations"
        description="Track merchant reimbursements, payout readiness, settlement balances, and Morph proof visibility.">
        <x-slot:actions>
            <div class="metric-current-view rounded-2xl border px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-ui-action">Current View</p>
                <p class="mt-1 text-2xl font-bold text-ui-anchor">{{ number_format($settlements->total()) }}</p>
                <p class="text-xs text-ui-subtext">
                    {{ filled($filters['status'] ?? null) ? $statusLabel($filters['status']) . ' records' : ($hasFilters ? 'Search results' : 'All settlement records') }}
                </p>
            </div>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border border-ui-border bg-ui-surface p-4 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Total Records</p>
            <p class="mt-1 text-2xl font-bold text-ui-text">{{ number_format($stats['total']) }}</p>
            <p class="mt-1 text-xs text-ui-subtext">Generated from claims</p>
        </div>

        <div class="rounded-2xl border border-ui-border bg-ui-surface p-4 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Pending</p>
            <p class="mt-1 text-2xl font-bold text-ui-warning">{{ number_format($stats['pending']) }}</p>
            <p class="mt-1 text-xs text-amber-600">Ready for release review</p>
        </div>

        <div class="rounded-2xl border border-ui-border bg-ui-surface p-4 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Released</p>
            <p class="mt-1 text-2xl font-bold text-ui-success">{{ number_format($stats['released']) }}</p>
            <p class="mt-1 text-xs text-emerald-600">Fully completed</p>
        </div>

        <div class="rounded-2xl border border-ui-border bg-ui-surface p-4 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Remaining Value</p>
            <p class="mt-1 text-2xl font-bold text-ui-text">&#8369;{{ number_format($stats['remaining_amount'], 2) }}</p>
            <p class="mt-1 text-xs text-amber-600">Outstanding balance</p>
        </div>

        <div class="rounded-2xl border border-ui-border bg-ui-surface p-4 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Released Value</p>
            <p class="mt-1 text-2xl font-bold text-ui-text">&#8369;{{ number_format($stats['released_amount'], 2) }}</p>
            <p class="mt-1 text-xs text-teal-600">Recorded payouts</p>
        </div>
    </div>

    <section class="rounded-2xl border border-teal-100 bg-teal-50 px-5 py-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <p class="max-w-4xl text-sm leading-6 text-teal-800">
                {{ $demoSafeNotice }}
            </p>

            <div class="flex flex-wrap gap-2">
                <x-status-badge status="GCash/PHP simulation" tone="proof" />
                <x-status-badge status="Morph proof" tone="success" />
                <x-status-badge status="Optional real EDUX transfer" tone="proof" />
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
        <form method="GET" action="{{ route('admin.settlements.index') }}" class="grid grid-cols-1 gap-4 lg:grid-cols-[1fr_16rem_auto_auto] lg:items-end">
            <div>
                <label for="search" class="block text-sm font-semibold text-slate-700">Search</label>
                <input id="search"
                       name="search"
                       type="search"
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Reference, member, merchant, or status"
                       class="mt-2 w-full rounded-xl border-slate-200 text-sm text-slate-700 shadow-sm focus:border-teal-500 focus:ring-teal-500">
            </div>

            <div>
                <label for="status" class="block text-sm font-semibold text-slate-700">Status</label>
                <select id="status"
                        name="status"
                        class="mt-2 w-full rounded-xl border-slate-200 text-sm text-slate-700 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    <option value="">All statuses</option>
                    <option value="Pending" @selected(($filters['status'] ?? null) === 'Pending')>Ready for Release</option>
                    <option value="Partially Released" @selected(($filters['status'] ?? null) === 'Partially Released')>Partially Released</option>
                    <option value="Released" @selected(($filters['status'] ?? null) === 'Released')>Released</option>
                </select>
            </div>

            <button type="submit"
                    class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-ui-action/20 bg-ui-action/10 px-5 py-2.5 text-sm font-semibold text-ui-action shadow-sm transition hover:bg-ui-action/15 lg:w-auto">
                Apply Filters
            </button>

            @if($hasFilters)
                <a href="{{ route('admin.settlements.index') }}"
                   class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-ui-border px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-ui-canvas lg:w-auto">
                    Clear
                </a>
            @endif
        </form>
    </section>

    <x-table-card
        title="Settlement Records"
        description="Showing {{ $settlements->firstItem() ?? 0 }} to {{ $settlements->lastItem() ?? 0 }} of {{ $settlements->total() }} records.">
        <div class="space-y-4">
            @forelse($settlements as $settlement)
                @php
                    $merchantProfile = $settlement->merchant?->merchantProfile;
                    $proofRecord = $proofRecords[$settlement->assistance_request_id] ?? null;
                    $proofData = $proofPayload($proofRecord);
                    $proofHash = $proofData['proof_hash'] ?? null;
                    $settlementProofRecord = $settlementProofRecords[$settlement->assistance_request_id] ?? null;
                    $settlementProofData = $proofPayload($settlementProofRecord);
                    $proofStatus = $proofRecord?->blockchain_status ?? 'Not recorded';
                    $isReleased = in_array($settlement->status, ['Released', 'Settled'], true);
                    $remainingBalance = $settlement->computed_remaining_balance;
                    $totalReleased = $settlement->computed_total_released;
                    $payoutReady = filled($merchantProfile?->payout_account_name) && filled($merchantProfile?->payout_account_number);
                    $latestPayout = $settlement->payouts->first();
                    $latestPayoutMetadata = $latestPayout?->metadata ?? [];
                @endphp

                <article x-data="{ payoutOpen: false, qrPreview: null }"
                         class="overflow-hidden rounded-2xl border border-ui-border bg-white shadow-sm shadow-slate-200/60">
                    <div class="grid gap-4 p-4 lg:grid-cols-[1.15fr_.9fr_.85fr_auto] lg:items-center">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-mono text-xs font-semibold text-ui-subtext">
                                    {{ $settlement->assistanceRequest->reference_code ?? 'N/A' }}
                                </p>
                                <x-status-badge :status="$statusLabel($settlement->status)" :tone="$statusTone($settlement->status)" size="xs" />
                            </div>

                            <p class="mt-2 text-base font-bold text-ui-text">
                                {{ $merchantProfile->business_name ?? $settlement->merchant->name ?? 'N/A' }}
                            </p>

                            <p class="mt-1 text-sm text-ui-subtext">
                                {{ $settlement->assistanceRequest->member->name ?? 'Member' }} &middot; {{ $settlement->assistanceRequest->program->program_name ?? 'Assistance program' }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <div class="flex items-start gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-teal-100 text-teal-700">
                                    <x-icon name="credit-card" size="h-4 w-4" />
                                </span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">GCash payout</p>
                                        <x-status-badge :status="$payoutReady ? 'Ready' : 'Missing details'" :tone="$payoutReady ? 'success' : 'warning'" size="xs" />
                                    </div>
                                    @if($payoutReady)
                                        <p class="mt-1 truncate text-sm font-semibold text-slate-800">{{ $merchantProfile->payout_account_name }}</p>
                                        <p class="font-mono text-xs text-slate-500">{{ $merchantProfile->payout_account_number }}</p>
                                    @else
                                        <p class="mt-1 text-xs leading-5 text-amber-700">
                                            Merchant payout details are required before releasing settlement.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2 rounded-xl border border-ui-border bg-ui-canvas/60 p-3 text-center">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Total</p>
                                <p class="mt-1 text-sm font-bold text-ui-text">&#8369;{{ number_format($settlement->amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Released</p>
                                <p class="mt-1 text-sm font-bold text-ui-success">&#8369;{{ number_format($totalReleased, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Remaining</p>
                                <p class="mt-1 text-sm font-bold text-ui-warning">&#8369;{{ number_format($remainingBalance, 2) }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 lg:items-end">
                            <div class="flex flex-wrap gap-2 lg:justify-end">
                                <x-status-badge
                                    :status="$proofStatus === 'Not recorded' ? 'Claim Proof Pending' : 'Claim Proof ' . $proofStatus"
                                    :tone="$proofStatus === 'Confirmed' ? 'success' : ($proofStatus === 'Failed' ? 'danger' : 'warning')"
                                    size="xs" />
                                @if($settlementProofRecord)
                                    <x-status-badge
                                        :status="'Payout Proof ' . $settlementProofRecord->blockchain_status"
                                        :tone="$settlementProofRecord->blockchain_status === 'Confirmed' ? 'success' : ($settlementProofRecord->blockchain_status === 'Failed' ? 'danger' : 'warning')"
                                        size="xs" />
                                @endif
                            </div>

                            @if(! $isReleased)
                                <button type="button"
                                        @click="payoutOpen = true"
                                        class="inline-flex min-h-10 w-full items-center justify-center rounded-xl bg-ui-action px-4 py-2 text-sm font-semibold text-white transition hover:bg-ui-anchor lg:w-auto">
                                    Release Payout
                                </button>
                            @else
                                <span class="inline-flex min-h-10 items-center rounded-xl bg-ui-canvas px-4 py-2 text-sm font-semibold text-ui-subtext">
                                    Released {{ $settlement->settled_at?->format('M d, Y') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid gap-0 border-t border-ui-border bg-slate-50/60 text-sm lg:grid-cols-3">
                        <div class="border-b border-ui-border p-4 lg:border-b-0 lg:border-r">
                            <p class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Settlement Rail</p>
                            <p class="mt-2 font-semibold text-ui-text">GCash/PHP simulation</p>
                            <p class="mt-1 text-xs leading-5 text-ui-subtext">Morph testnet proof with optional EDUX transfer</p>
                            @if($latestPayout)
                                <div class="mt-2">
                                    <x-status-badge :status="$eduxLabel($latestPayoutMetadata)" :tone="$eduxTone($latestPayoutMetadata)" size="xs" />
                                </div>
                                @if($latestPayoutMetadata['edux_transaction_hash'] ?? null)
                                    <p class="mt-2 break-all font-mono text-[11px] text-teal-800">{{ $latestPayoutMetadata['edux_transaction_hash'] }}</p>
                                @endif
                            @endif
                        </div>

                        <div class="border-b border-ui-border p-4 lg:border-b-0 lg:border-r">
                            <p class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Proof Metadata</p>
                            @if($settlementProofRecord)
                                <p class="mt-2 truncate font-mono text-xs font-semibold text-teal-800">{{ $settlementProofData['settlement_reference'] ?? 'Settlement reference recorded' }}</p>
                                @if($settlementProofData['proof_hash'] ?? null)
                                    <p class="mt-1 break-all font-mono text-[11px] text-ui-subtext">{{ substr($settlementProofData['proof_hash'], 0, 16) }}...{{ substr($settlementProofData['proof_hash'], -12) }}</p>
                                @endif
                            @elseif($proofHash)
                                <p class="mt-2 break-all font-mono text-[11px] text-ui-subtext">{{ substr($proofHash, 0, 16) }}...{{ substr($proofHash, -12) }}</p>
                            @else
                                <p class="mt-2 text-xs text-ui-subtext">Proof metadata appears after payout release.</p>
                            @endif
                        </div>

                        <div class="p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Latest Payout Snapshot</p>
                            @if($latestPayout)
                                <p class="mt-2 font-semibold text-ui-text">&#8369;{{ number_format((float) $latestPayout->amount, 2) }} &middot; {{ ucfirst($latestPayout->payout_type) }}</p>
                                <p class="mt-1 text-xs text-ui-subtext">{{ $latestPayout->payout_account_name_used ?: 'GCash name not captured' }}</p>
                                <p class="font-mono text-xs text-ui-subtext">{{ $latestPayout->payout_account_number_used ?: 'GCash number not captured' }}</p>
                                <p class="mt-2 text-xs font-semibold text-teal-700">{{ $eduxLabel($latestPayoutMetadata) }}</p>
                            @else
                                <p class="mt-2 text-xs text-ui-subtext">No payout event recorded yet.</p>
                            @endif
                        </div>
                    </div>

                    <div x-cloak
                         x-show="payoutOpen"
                         x-transition.opacity
                         class="fixed inset-0 z-[80] flex items-center justify-center overflow-y-auto px-4 py-6"
                         role="dialog"
                         aria-modal="true">
                        <div class="absolute inset-0 bg-slate-950/45" @click="payoutOpen = false"></div>

                        <section x-show="payoutOpen"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="translate-y-4 scale-[0.98] opacity-0"
                                 x-transition:enter-end="translate-y-0 scale-100 opacity-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="translate-y-0 scale-100 opacity-100"
                                 x-transition:leave-end="translate-y-4 scale-[0.98] opacity-0"
                                 class="relative flex max-h-[92vh] w-full max-w-3xl flex-col overflow-hidden rounded-3xl border border-ui-border bg-white shadow-2xl shadow-slate-950/20">
                            <div class="flex items-start justify-between gap-4 border-b border-ui-border px-5 py-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wider text-ui-action">Payout Release</p>
                                    <h3 class="mt-1 text-lg font-bold text-ui-text">Settlement #{{ $settlement->id }}</h3>
                                    <p class="mt-1 font-mono text-xs text-ui-subtext">{{ $settlement->assistanceRequest->reference_code ?? 'N/A' }}</p>
                                </div>

                                <button type="button"
                                        @click="payoutOpen = false"
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-ui-border text-ui-subtext transition hover:bg-ui-canvas hover:text-ui-text"
                                        aria-label="Close payout panel">
                                    <x-icon name="x" size="h-5 w-5" />
                                </button>
                            </div>

                            <div class="min-h-0 flex-1 space-y-4 overflow-y-auto px-5 py-5">
                                <div class="rounded-2xl border {{ $payoutReady ? 'border-teal-100 bg-teal-50' : 'border-amber-200 bg-amber-50' }} p-4">
                                    <div class="flex gap-3">
                                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl {{ $payoutReady ? 'bg-teal-100 text-teal-700' : 'bg-amber-100 text-amber-700' }}">
                                            <x-icon name="credit-card" size="h-5 w-5" />
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p class="text-sm font-bold {{ $payoutReady ? 'text-teal-900' : 'text-amber-900' }}">GCash Payout Details</p>
                                                <x-status-badge :status="$payoutReady ? 'Ready' : 'Missing payout details'" :tone="$payoutReady ? 'success' : 'warning'" size="xs" />
                                            </div>

                                            @if($payoutReady)
                                                <dl class="mt-3 grid grid-cols-1 gap-3 text-sm sm:grid-cols-[1fr_auto]">
                                                    <div>
                                                        <dt class="text-xs font-semibold uppercase tracking-wide text-teal-700">Account Name</dt>
                                                        <dd class="mt-1 font-semibold text-teal-950">{{ $merchantProfile->payout_account_name }}</dd>
                                                        <dt class="mt-3 text-xs font-semibold uppercase tracking-wide text-teal-700">Mobile Number</dt>
                                                        <dd class="mt-1 font-mono font-semibold text-teal-950">{{ $merchantProfile->payout_account_number }}</dd>
                                                    </div>
                                                    <div class="sm:text-right">
                                                        @if($merchantProfile->payout_qr)
                                                            <button type="button"
                                                                    @click="qrPreview = @js($payoutQrUrl($merchantProfile->payout_qr))"
                                                                    class="rounded-xl border border-teal-200 bg-white p-1 transition hover:shadow-md sm:ml-auto">
                                                                <img src="{{ $payoutQrUrl($merchantProfile->payout_qr) }}"
                                                                     alt="GCash payout QR for {{ $merchantProfile->business_name ?? 'merchant' }}"
                                                                     class="h-24 w-24 rounded-lg bg-white object-cover">
                                                            </button>
                                                            <p class="mt-2 text-xs font-semibold text-teal-700">QR available</p>
                                                        @else
                                                            <p class="rounded-xl bg-white/70 px-3 py-2 text-xs font-semibold text-teal-700 ring-1 ring-teal-100">No QR uploaded</p>
                                                        @endif
                                                    </div>
                                                </dl>
                                                @if($merchantProfile->payout_notes)
                                                    <p class="mt-3 border-t border-teal-200/70 pt-3 text-sm leading-6 text-teal-800">{{ $merchantProfile->payout_notes }}</p>
                                                @endif
                                            @else
                                                <p class="mt-3 text-sm leading-6 text-amber-800">
                                                    Merchant payout details are required before releasing settlement.
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    <div class="rounded-2xl border border-ui-border bg-ui-canvas/70 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Settlement Total</p>
                                        <p class="mt-1 text-lg font-bold text-ui-text">&#8369;{{ number_format($settlement->amount, 2) }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Released</p>
                                        <p class="mt-1 text-lg font-bold text-emerald-800">&#8369;{{ number_format($totalReleased, 2) }}</p>
                                    </div>
                                    <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4">
                                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Remaining</p>
                                        <p class="mt-1 text-lg font-bold text-amber-800">&#8369;{{ number_format($remainingBalance, 2) }}</p>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-ui-border bg-white p-4">
                                    <p class="text-sm font-bold text-ui-text">Payout History</p>
                                    <div class="mt-3 divide-y divide-ui-border">
                                        @forelse($settlement->payouts->take(5) as $payout)
                                            @php($payoutMetadata = $payout->metadata ?? [])
                                            <div class="py-3 first:pt-0 last:pb-0">
                                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                                    <div>
                                                        <p class="font-semibold text-ui-text">&#8369;{{ number_format((float) $payout->amount, 2) }} {{ ucfirst($payout->payout_type) }}</p>
                                                        <p class="mt-1 text-xs text-ui-subtext">{{ $payout->released_at->format('M d, Y g:i A') }}</p>
                                                        <div class="mt-2">
                                                            <x-status-badge :status="$eduxLabel($payoutMetadata)" :tone="$eduxTone($payoutMetadata)" size="xs" />
                                                        </div>
                                                        @if($payoutMetadata['edux_transaction_hash'] ?? null)
                                                            <p class="mt-2 break-all font-mono text-[11px] text-teal-800">{{ $payoutMetadata['edux_transaction_hash'] }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="min-w-0 sm:text-right">
                                                        <p class="text-sm font-semibold text-slate-700">{{ $payout->payout_account_name_used ?: 'GCash name not captured' }}</p>
                                                        <p class="font-mono text-xs text-ui-subtext">{{ $payout->payout_account_number_used ?: 'GCash number not captured' }}</p>
                                                        @if($payoutMetadata['edux_to'] ?? null)
                                                            <p class="mt-1 break-all font-mono text-[11px] text-ui-subtext">{{ $payoutMetadata['edux_amount'] ?? '1' }} {{ $payoutMetadata['edux_token_symbol'] ?? 'EDUX' }} to {{ $payoutMetadata['edux_to'] }}</p>
                                                        @endif
                                                        @if($payout->payout_notes_used)
                                                            <p class="mt-1 max-w-xs text-xs leading-5 text-ui-subtext">{{ $payout->payout_notes_used }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="py-3 text-sm text-ui-subtext">No payout event recorded yet.</p>
                                        @endforelse
                                    </div>
                                </div>

                                <p class="rounded-2xl border border-cyan-100 bg-cyan-50 px-4 py-3 text-sm leading-6 text-cyan-800">
                                    {{ $demoSafeNotice }}
                                </p>
                            </div>

                            <div class="border-t border-ui-border bg-ui-canvas/70 px-5 py-4">
                                @if(! $payoutReady)
                                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                                        <p class="text-sm font-semibold text-amber-800">Merchant payout details are required before releasing settlement.</p>
                                    </div>
                                @else
                                    <form method="POST"
                                          action="{{ route('admin.settlements.settle', $settlement) }}"
                                          data-confirm
                                          data-confirm-title="Confirm PHP payout release?"
                                          data-confirm-message="This simulates a GCash/PHP payout and records Morph proof metadata. If EDUX_DEMO_TRANSFER_ENABLED is true, a real EDUX ERC-20 testnet transfer will also be executed by the backend operator wallet."
                                          data-confirm-button="Confirm payout"
                                          data-confirm-tone="success"
                                          data-loading-text="Releasing payout..."
                                          data-loader-title="Releasing payout..."
                                          data-loader-message="Updating the reimbursement lifecycle and notifying the merchant.">
                                        @csrf

                                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-[1fr_12rem_auto] sm:items-end">
                                            <div>
                                                <label class="block text-xs font-semibold uppercase tracking-wide text-ui-subtext">Payout Mode</label>
                                                <select name="payout_type" class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                                                    <option value="full">Full payout - remaining balance</option>
                                                    <option value="partial">Partial payout</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-semibold uppercase tracking-wide text-ui-subtext">Partial Amount</label>
                                                <input type="number"
                                                       name="partial_amount"
                                                       min="0.01"
                                                       max="{{ $remainingBalance }}"
                                                       step="0.01"
                                                       placeholder="Optional"
                                                       class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-teal-500 focus:ring-teal-500">
                                            </div>

                                            <button type="submit"
                                                    class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-ui-action px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor sm:w-auto">
                                                Confirm Payout
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </section>
                    </div>

                    <div x-cloak
                         x-show="qrPreview"
                         x-transition.opacity
                         class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/70 px-4 py-6"
                         @click.self="qrPreview = null">
                        <div class="w-full max-w-lg rounded-3xl border border-white/10 bg-white p-4 shadow-2xl">
                            <div class="flex items-center justify-between gap-3 pb-3">
                                <p class="text-sm font-bold text-ui-text">GCash QR Preview</p>
                                <button type="button"
                                        @click="qrPreview = null"
                                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-ui-border text-ui-subtext hover:bg-ui-canvas"
                                        aria-label="Close QR preview">
                                    <x-icon name="x" size="h-5 w-5" />
                                </button>
                            </div>
                            <img :src="qrPreview"
                                 alt="Expanded GCash QR preview"
                                 class="max-h-[70vh] w-full rounded-2xl bg-white object-contain">
                        </div>
                    </div>
                </article>
            @empty
                <div class="px-4 py-14 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-ui-canvas text-ui-subtext">
                        <x-icon name="credit-card" size="h-8 w-8" />
                    </div>

                    <h3 class="mt-5 text-lg font-semibold text-ui-text">No settlement records found</h3>
                    <p class="mx-auto mt-2 max-w-md text-sm text-ui-subtext">
                        {{ $hasFilters
                            ? 'No settlement records match the selected search or status. Clear filters to return to the full console.'
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
