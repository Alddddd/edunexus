@extends('layouts.dashboard')

@section('title', 'Merchant Settlements')

@section('content')
@php
    $statusTone = fn ($status) => match ($status) {
        'Released', 'Settled' => 'success',
        'Partially Released' => 'proof',
        default => 'warning',
    };

    $statusLabel = fn ($status) => match ($status) {
        'Released', 'Settled' => 'Payout released',
        'Partially Released' => 'Partially released',
        default => 'Pending payout',
    };

    $proofTone = fn (?string $status) => match ($status) {
        'Confirmed' => 'success',
        'Failed' => 'danger',
        'Pending' => 'warning',
        default => 'neutral',
    };

    $explorerBaseUrl = 'https://explorer-hoodi.morph.network/tx/';
    $canOpenExplorer = fn (?string $hash) => filled($hash) && preg_match('/^0x[a-fA-F0-9]{64}$/', (string) $hash);
@endphp

<div class="w-full min-w-0 max-w-none space-y-5">
    <x-page-header
        title="Settlement History"
        eyebrow="Merchant Reimbursements"
        description="Review your peso settlement records, payout release status, remaining balances, and Morph proof visibility.">
        <x-slot:actions>
            <a href="{{ route('merchant.payout-settings.edit') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-xl border border-ui-border bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas">
                Payout Settings
            </a>
        </x-slot:actions>
    </x-page-header>

    <x-table-card
        title="Settlement Records"
        description="Showing {{ $settlements->firstItem() ?? 0 }} to {{ $settlements->lastItem() ?? 0 }} of {{ $settlements->total() }} merchant-scoped records.">
        <div class="space-y-4 p-4">
            @forelse($settlements as $settlement)
                @php
                    $claim = $settlement->assistanceRequest;
                    $latestPayout = $settlement->payouts->first();
                    $proofRecord = $proofRecords[$settlement->assistance_request_id] ?? null;
                    $latestPayoutMetadata = $latestPayout?->metadata ?? [];
                    $releasedAmount = (float) ($settlement->total_released ?? 0);
                    $remainingAmount = $settlement->computed_remaining_balance;
                    $proofHash = $latestPayout?->transaction_hash ?? $proofRecord?->transaction_hash;
                    $proofStatus = $latestPayout?->blockchain_status ?? $proofRecord?->blockchain_status;
                    $eduxHash = $latestPayoutMetadata['edux_transaction_hash'] ?? null;
                    $eduxStatus = $latestPayoutMetadata['edux_transfer_status'] ?? null;
                @endphp

                <article class="overflow-hidden rounded-2xl border border-ui-border bg-white shadow-sm shadow-slate-200/60">
                    <div class="grid gap-4 p-4 lg:grid-cols-[1.1fr_1.45fr_.9fr] lg:items-center">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <x-status-badge :status="$statusLabel($settlement->status)" :tone="$statusTone($settlement->status)" size="xs" />
                                <x-status-badge :status="$proofStatus ? 'Proof ' . $proofStatus : 'Proof pending'" :tone="$proofTone($proofStatus)" size="xs" />
                            </div>

                            <p class="mt-3 text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Linked Claim</p>
                            <p class="mt-1 break-all font-mono text-sm font-bold text-ui-text">{{ $claim?->reference_code ?? 'No claim reference' }}</p>
                            <p class="mt-2 text-sm text-ui-subtext">{{ $claim?->member?->name ?? 'Member' }} &middot; {{ $claim?->program?->program_name ?? 'Assistance program' }}</p>
                        </div>

                        <div class="grid gap-2 sm:grid-cols-4">
                            <div class="rounded-xl border border-ui-border bg-ui-canvas/70 px-3 py-2">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Amount</p>
                                <p class="mt-1 text-sm font-bold text-ui-text">&#8369;{{ number_format((float) $settlement->amount, 2) }}</p>
                            </div>

                            <div class="rounded-xl border border-emerald-100 bg-emerald-50/80 px-3 py-2">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-emerald-700">Released</p>
                                <p class="mt-1 text-sm font-bold text-ui-success">&#8369;{{ number_format($releasedAmount, 2) }}</p>
                            </div>

                            <div class="rounded-xl border border-amber-100 bg-amber-50/80 px-3 py-2">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-amber-700">Remaining</p>
                                <p class="mt-1 text-sm font-bold text-ui-warning">&#8369;{{ number_format($remainingAmount, 2) }}</p>
                            </div>

                            <div class="rounded-xl border border-ui-border bg-white px-3 py-2">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Latest Payout</p>
                                <p class="mt-1 text-xs font-semibold text-ui-text">{{ $latestPayout?->released_at?->format('M d, g:i A') ?? 'Pending release' }}</p>
                            </div>
                        </div>

                        <div class="min-w-0 rounded-xl border border-ui-border bg-ui-canvas/70 p-3">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Proof / Payout Rail</p>
                            <p class="mt-1 break-all font-mono text-xs font-semibold text-ui-text">{{ $proofHash ?: 'Generated after payout release' }}</p>

                            <div class="mt-3 flex flex-wrap gap-2">
                                @if($canOpenExplorer($proofHash))
                                    <a href="{{ $explorerBaseUrl . $proofHash }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-9 items-center rounded-lg border border-ui-proof/20 bg-ui-proof/10 px-3 py-1.5 text-xs font-semibold text-ui-proof transition hover:bg-ui-proof/15">
                                        View Morph
                                    </a>
                                @endif

                                @if($canOpenExplorer($eduxHash))
                                    <a href="{{ $explorerBaseUrl . $eduxHash }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-9 items-center rounded-lg border border-teal-100 bg-white px-3 py-1.5 text-xs font-semibold text-teal-700 transition hover:bg-teal-50">
                                        View EDUX
                                    </a>
                                @elseif($latestPayout)
                                    <span class="inline-flex min-h-9 items-center rounded-lg bg-white px-3 py-1.5 text-xs font-semibold text-ui-subtext ring-1 ring-ui-border">
                                        {{ $eduxStatus === 'failed' ? 'EDUX failed safely' : 'EDUX transfer not linked' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-0 border-t border-ui-border bg-slate-50/70 text-sm md:grid-cols-3">
                        <div class="border-b border-ui-border p-4 md:border-b-0 md:border-r">
                            <p class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Settlement Reference</p>
                            <p class="mt-1 break-all font-mono text-xs font-semibold text-ui-text">{{ $latestPayout?->settlement_reference ?? 'Settlement #' . $settlement->id }}</p>
                        </div>

                        <div class="border-b border-ui-border p-4 md:border-b-0 md:border-r">
                            <p class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Claim State</p>
                            <p class="mt-1 font-semibold text-ui-text">{{ $claim?->is_claimed ? 'Merchant validated and processed' : 'Waiting for merchant validation' }}</p>
                        </div>

                        <div class="p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Payout Destination</p>
                            <p class="mt-1 font-semibold text-ui-text">{{ $latestPayout?->payout_account_name_used ?? 'Captured on release' }}</p>
                        </div>
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
