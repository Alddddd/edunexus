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
                @endphp

                <article class="p-4 sm:p-5">
                    <div class="grid gap-4 lg:grid-cols-[1.2fr_.8fr_.8fr] lg:items-start">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-mono text-xs font-semibold text-ui-subtext">
                                    {{ $settlement->assistanceRequest->reference_code ?? 'No reference' }}
                                </p>
                                <x-status-badge :status="$statusLabel($settlement->status)" :tone="$statusTone($settlement->status)" size="xs" />
                                @if($proofRecord)
                                    <x-status-badge status="Morph proof recorded" tone="proof" size="xs" />
                                @endif
                            </div>

                            <p class="mt-2 font-semibold text-ui-text">
                                {{ $settlement->assistanceRequest->member->name ?? 'Member' }}
                            </p>
                            <p class="mt-1 text-sm text-ui-subtext">
                                {{ $settlement->assistanceRequest->program->program_name ?? 'Assistance program' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-3 gap-2 rounded-xl border border-ui-border bg-ui-canvas/70 p-3 text-center">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Total</p>
                                <p class="mt-1 text-sm font-bold text-ui-text">&#8369;{{ number_format((float) $settlement->amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Released</p>
                                <p class="mt-1 text-sm font-bold text-ui-success">&#8369;{{ number_format((float) $settlement->total_released, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Remaining</p>
                                <p class="mt-1 text-sm font-bold text-ui-warning">&#8369;{{ number_format((float) $settlement->remaining_balance, 2) }}</p>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Latest payout</p>
                            @if($latestPayout)
                                <p class="mt-1 font-semibold text-ui-text">&#8369;{{ number_format((float) $latestPayout->amount, 2) }}</p>
                                <p class="mt-1 text-xs text-ui-subtext">{{ $latestPayout->released_at->format('M d, Y g:i A') }}</p>
                            @else
                                <p class="mt-1 text-sm text-ui-subtext">No payout released yet.</p>
                            @endif
                        </div>
                    </div>

                    @if($settlement->payouts->isNotEmpty())
                        <div class="mt-4 rounded-xl border border-ui-border bg-white">
                            <div class="border-b border-ui-border px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Payout history</p>
                            </div>
                            <div class="divide-y divide-ui-border/70">
                                @foreach($settlement->payouts as $payout)
                                    <div class="grid gap-2 px-4 py-3 text-sm sm:grid-cols-[1fr_auto] sm:items-center">
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
