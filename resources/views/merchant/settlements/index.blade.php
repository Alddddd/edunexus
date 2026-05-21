@extends('layouts.dashboard')

@section('title', 'Merchant Settlements')

@section('content')
@php
    $statusTone = fn ($status) => in_array($status, ['Released', 'Settled'], true)
        ? 'success'
        : ($status === 'Partially Released' ? 'proof' : 'warning');

    $statusLabel = fn ($status) => match ($status) {
        'Released', 'Settled' => 'Payout released',
        'Partially Released' => 'Partially released',
        default => 'Pending payout',
    };
    $explorerBaseUrl = 'https://explorer-hoodi.morph.network/tx/';
    $canOpenExplorer = fn (?string $hash) => filled($hash) && str_starts_with((string) $hash, '0x');
    $eduxOperationalLabel = function (?array $metadata, bool $hasPayout) {
        if (! $hasPayout) {
            return 'Waiting for settlement release';
        }

        return match ($metadata['edux_transfer_status'] ?? null) {
            'success' => 'EDUX transfer recorded',
            'failed' => 'EDUX transfer failed safely',
            default => 'EDUX transfer disabled for this payout',
        };
    };
@endphp

<div class="w-full min-w-0 max-w-7xl space-y-5">
    <x-page-header
        title="Settlement History"
        eyebrow="Merchant Reimbursements"
        description="Review your own peso settlement records, payout releases, and remaining balances.">
        <x-slot:actions>
            <a href="{{ route('merchant.payout-settings.edit') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-xl border border-ui-border bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas">
                Payout Settings
            </a>
        </x-slot:actions>
    </x-page-header>

    <x-table-card
        title="Payment Records"
        description="Showing {{ $settlements->firstItem() ?? 0 }} to {{ $settlements->lastItem() ?? 0 }} of {{ $settlements->total() }} settlement records.">
        <div class="divide-y divide-ui-border/80">
            @forelse($settlements as $settlement)
                @php
                    $latestPayout = $settlement->payouts->first();
                    $proofRecord = $proofRecords[$settlement->assistance_request_id] ?? null;
                    $latestPayoutMetadata = $latestPayout?->metadata ?? [];
                    $releasedAmount = (float) ($settlement->total_released ?? 0);
                    $settlementIdentityLabel = $latestPayout?->settlement_reference ? 'Payout Reference' : 'Settlement Reference';
                    $settlementIdentity = $latestPayout?->settlement_reference ?? 'Settlement #' . $settlement->id;
                    $proofState = $latestPayout?->blockchain_status
                        ? 'Settlement proof ' . strtolower($latestPayout->blockchain_status)
                        : 'Generated after payout release';
                @endphp

                <article x-data="{ open: false }" class="p-4">
                    <button type="button"
                            @click="open = !open"
                            class="group w-full rounded-2xl border border-ui-border bg-gradient-to-br from-white via-ui-canvas/70 to-teal-50/40 p-4 text-left shadow-sm shadow-slate-200/60 transition hover:border-ui-action/20 hover:shadow-md">
                        <div class="grid gap-4 xl:grid-cols-[1.1fr_1.2fr_auto] xl:items-center">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-status-badge :status="$statusLabel($settlement->status)" :tone="$statusTone($settlement->status)" size="xs" />
                                    <x-status-badge :status="$proofRecord ? 'Morph proof recorded' : 'Proof pending'" :tone="$proofRecord ? 'proof' : 'warning'" size="xs" />
                                </div>

                                <p class="mt-3 text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">{{ $settlementIdentityLabel }}</p>
                                <p class="mt-1 break-all font-mono text-sm font-bold text-ui-text">{{ $settlementIdentity }}</p>
                                <p class="mt-2 text-xs text-ui-subtext">Cooperative reimbursement record</p>

                                <div class="mt-3 rounded-xl border border-ui-border bg-white/80 px-3 py-2">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Linked Claim</p>
                                    <p class="mt-1 break-all font-mono text-xs font-semibold text-ui-text">{{ $settlement->assistanceRequest->reference_code ?? 'No claim reference' }}</p>
                                </div>
                            </div>

                            <div class="grid min-w-0 gap-2.5 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
                                <div class="rounded-xl border border-ui-border bg-white/80 px-3 py-2">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Member</p>
                                    <p class="mt-1 truncate text-sm font-semibold text-ui-text">{{ $settlement->assistanceRequest->member->name ?? 'Member' }}</p>
                                    <p class="mt-1 truncate text-xs text-ui-subtext">{{ $settlement->assistanceRequest->program->program_name ?? 'Assistance program' }}</p>
                                </div>
                                <div class="rounded-xl border border-ui-border bg-white/80 px-3 py-2">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Total</p>
                                    <p class="mt-1 text-sm font-bold text-ui-text">&#8369;{{ number_format((float) $settlement->amount, 2) }}</p>
                                </div>
                                <div class="rounded-xl border border-emerald-100 bg-emerald-50/70 px-3 py-2">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-700">Released</p>
                                    <p class="mt-1 text-sm font-bold text-ui-success">&#8369;{{ number_format($releasedAmount, 2) }}</p>
                                </div>
                                <div class="rounded-xl border border-amber-100 bg-amber-50/70 px-3 py-2">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-amber-700">Remaining</p>
                                    <p class="mt-1 text-sm font-bold text-ui-warning">&#8369;{{ number_format((float) $settlement->remaining_balance, 2) }}</p>
                                </div>
                                <div class="rounded-xl border border-ui-border bg-white/80 px-3 py-2 sm:col-span-2 lg:col-span-4 xl:col-span-1">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Latest Payout</p>
                                    <p class="mt-1 text-xs font-semibold text-ui-text">{{ $latestPayout?->released_at?->format('M d, g:i A') ?? 'Pending release' }}</p>
                                    <p class="mt-1 text-[11px] text-ui-subtext">{{ $proofState }}</p>
                                </div>
                            </div>

                            <span class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-ui-border bg-white px-3 text-xs font-semibold text-ui-action shadow-sm transition group-hover:border-ui-action/20 group-hover:bg-ui-action/10">
                                <span x-text="open ? 'Hide details' : 'Review details'">Review details</span>
                                <x-icon name="chevron-right" size="h-3.5 w-3.5 transition-transform duration-200" x-bind:class="open ? 'rotate-90' : ''" />
                            </span>
                        </div>
                    </button>

                    <div x-cloak
                         x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="mt-4 space-y-4">
                        <div class="grid gap-4 xl:grid-cols-[.85fr_1fr] xl:items-start">
                            <div class="hidden">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Settlement Snapshot</p>
                                        <p class="mt-1 text-xs text-ui-subtext">Financial receipt summary</p>
                                    </div>
                                    <x-status-badge :status="$latestPayout ? 'Payout event recorded' : 'Awaiting settlement release'" :tone="$latestPayout ? 'success' : 'warning'" size="xs" />
                                </div>

                                <dl class="mt-3 grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Approved</dt>
                                        <dd class="mt-1 font-bold text-ui-text">&#8369;{{ number_format((float) $settlement->amount, 2) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Released</dt>
                                        <dd class="mt-1 font-bold text-ui-success">&#8369;{{ number_format($releasedAmount, 2) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Remaining</dt>
                                        <dd class="mt-1 font-bold text-ui-warning">&#8369;{{ number_format((float) $settlement->remaining_balance, 2) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Payout Time</dt>
                                        <dd class="mt-1 text-xs font-semibold text-ui-text">{{ $latestPayout?->released_at?->format('M d, g:i A') ?? 'Pending release' }}</dd>
                                    </div>
                                    <div class="col-span-2 border-t border-ui-border/70 pt-2">
                                        <dt class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Proof State</dt>
                                        <dd class="mt-1 text-xs font-semibold text-ui-text">{{ $proofState }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="rounded-xl border border-ui-border bg-white/90 p-3 shadow-sm shadow-slate-200/60">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Linked Workflow Chain</p>
                                        <p class="mt-1 text-xs text-ui-subtext">Claim to settlement trace</p>
                                    </div>
                                </div>

                                <div class="mt-3 flex flex-wrap gap-2 text-[11px] font-semibold">
                                    @foreach([
                                        ['Request Submitted', 'done'],
                                        ['Approved', 'done'],
                                        ['Merchant Validated', 'done'],
                                        ['Settlement Generated', $settlement ? 'done' : 'pending'],
                                        [in_array($settlement->status, ['Released', 'Settled'], true) ? 'Settlement Released' : 'Awaiting Settlement Release', in_array($settlement->status, ['Released', 'Settled'], true) ? 'done' : 'pending'],
                                        [$eduxOperationalLabel($latestPayoutMetadata, (bool) $latestPayout), ($latestPayoutMetadata['edux_transfer_status'] ?? null) === 'success' ? 'done' : 'pending'],
                                    ] as [$label, $state])
                                        <span class="rounded-full px-2.5 py-1 {{ $state === 'done' ? 'bg-emerald-50 text-ui-success ring-1 ring-emerald-100' : 'bg-amber-50 text-ui-warning ring-1 ring-amber-100' }}">{{ $label }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-ui-border bg-white/90 p-3 shadow-sm shadow-slate-200/60">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Settlement Record</p>
                                    <p class="mt-1 text-xs text-ui-subtext">Payout proof and reimbursement reference</p>
                                </div>
                                @if($latestPayout)
                                    <x-status-badge :status="$latestPayout->blockchain_status ?: 'Recorded'" :tone="$latestPayout->blockchain_status === 'Confirmed' ? 'success' : 'warning'" size="xs" />
                                @endif
                            </div>
                            @if($latestPayout)
                                <div class="mt-3 rounded-xl border border-teal-100 bg-teal-50/70 px-3 py-2">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-teal-900">&#8369;{{ number_format((float) $latestPayout->amount, 2) }} {{ ucfirst($latestPayout->payout_type) }}</p>
                                            <p class="mt-1 text-xs text-teal-700">
                                                {{ ($latestPayoutMetadata['edux_transfer_status'] ?? null) === 'success' ? 'Real EDUX transfer recorded' : 'Settlement proof recorded on Morph' }}
                                            </p>
                                        </div>
                                        @if($canOpenExplorer($latestPayout->transaction_hash))
                                            <a href="{{ $explorerBaseUrl . $latestPayout->transaction_hash }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-9 items-center justify-center rounded-lg border border-teal-100 bg-white px-3 py-1.5 text-xs font-semibold text-teal-700 transition hover:bg-teal-50">View Settlement Record</a>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <p class="mt-2 text-sm text-ui-subtext">Settlement record appears after payout release.</p>
                            @endif
                        </div>

                        @if($settlement->payouts->isNotEmpty())
                            <div class="rounded-xl border border-ui-border bg-white">
                                <div class="flex items-center justify-between gap-3 border-b border-ui-border px-4 py-2.5">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Payout history</p>
                                    <span class="text-xs font-semibold text-ui-subtext">{{ $settlement->payouts->count() }} release event{{ $settlement->payouts->count() === 1 ? '' : 's' }}</span>
                                </div>
                                <div class="divide-y divide-ui-border/70">
                                    @foreach($settlement->payouts->take(5) as $payout)
                                        <div class="grid gap-2 px-4 py-2.5 text-sm sm:grid-cols-[1fr_auto] sm:items-center">
                                            <div>
                                                <p class="font-semibold text-ui-text">&#8369;{{ number_format((float) $payout->amount, 2) }} {{ ucfirst($payout->payout_type) }}</p>
                                                <p class="mt-1 text-xs text-ui-subtext">{{ $payout->released_at->format('M d, Y g:i A') }}</p>
                                            </div>
                                            <p class="text-xs font-semibold text-emerald-700 sm:text-right">
                                                {{ $payout->blockchain_status === 'Confirmed' ? 'Proof confirmed' : 'Proof recorded' }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </article>
            @empty
                <div class="px-6 py-14 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-ui-canvas text-ui-subtext">
                        <x-icon name="credit-card" size="h-8 w-8" />
                    </div>
                    <h3 class="mt-5 text-lg font-semibold text-ui-text">No settlements yet</h3>
                    <p class="mt-2 text-sm text-ui-subtext">Processed claims will appear here with reimbursement status.</p>
                </div>
            @endforelse
        </div>

        <x-slot:footer>
            <div class="flex flex-col items-center justify-center gap-4 text-center">
                <p class="text-sm text-ui-subtext">
                    Showing {{ $settlements->firstItem() ?? 0 }} to {{ $settlements->lastItem() ?? 0 }} of {{ $settlements->total() }} records
                </p>

                <div class="flex max-w-full justify-center overflow-x-auto pb-1">
                    {{ $settlements->links() }}
                </div>
            </div>
        </x-slot:footer>
    </x-table-card>
</div>
@endsection
