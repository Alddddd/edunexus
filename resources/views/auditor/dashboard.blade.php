@extends('layouts.dashboard')

@section('title', 'Auditor Dashboard')

@section('content')

<div class="w-full min-w-0 max-w-7xl space-y-5 text-ui-anchor">
    <section class="rounded-2xl border border-ui-border/80 bg-gradient-to-br from-ui-surface via-ui-surface/90 to-ui-proof/10 p-5 shadow-[0_22px_52px_rgba(15,47,44,0.10)] ring-1 ring-ui-anchor/5">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold uppercase tracking-wider text-ui-action">
                    Verification Review
                </p>

                <h1 class="mt-2 text-3xl font-bold text-ui-anchor">
                    Auditor Dashboard
                </h1>

                <p class="mt-2 max-w-3xl leading-6 text-ui-subtext/90">
                    Monitor claim activity and blockchain verification records for audit transparency.
                </p>
            </div>

            <span class="inline-flex w-fit rounded-xl border border-ui-action/20 bg-ui-surface/80 px-4 py-2 text-sm font-semibold text-ui-action shadow-sm shadow-ui-anchor/5 ring-1 ring-ui-action/10">
                Read-only verification
            </span>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-proof bg-ui-surface/95 p-5 shadow-[0_16px_38px_rgba(15,47,44,0.08)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-ui-subtext">Total Processed Claims</p>
            <p class="mt-2 text-3xl font-bold text-ui-text">{{ number_format($totalClaims) }}</p>
            <p class="mt-1 text-sm text-cyan-600">Merchant-processed assistance</p>
        </div>

        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-success bg-ui-surface/95 p-5 shadow-[0_16px_38px_rgba(15,47,44,0.08)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-ui-subtext">Confirmed Proofs</p>
            <p class="mt-2 text-3xl font-bold text-ui-success">{{ number_format($confirmedProofs) }}</p>
            <p class="mt-1 text-sm text-emerald-600">Verified blockchain records</p>
        </div>

        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-warning bg-ui-surface/95 p-5 shadow-[0_16px_38px_rgba(15,47,44,0.08)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-ui-subtext">Pending Proofs</p>
            <p class="mt-2 text-3xl font-bold text-ui-warning">{{ number_format($pendingProofs) }}</p>
            <p class="mt-1 text-sm text-amber-600">Awaiting confirmation</p>
        </div>
    </div>

    <section class="rounded-2xl border border-ui-action/15 bg-gradient-to-br from-ui-action/10 via-ui-surface/90 to-ui-proof/10 p-5 shadow-[0_20px_44px_rgba(11,93,86,0.10)] ring-1 ring-ui-anchor/5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold uppercase tracking-wider text-ui-action">
                    Audit Trail
                </p>

                <h2 class="mt-2 text-xl font-bold text-ui-anchor">
                    Claim Activity to Morph Proof Review
                </h2>

                <p class="mt-2 max-w-3xl text-sm leading-6 text-ui-subtext/90">
                    Review proof status, reference codes, recorded timestamps, and transaction hashes without exposing wallet mechanics.
                </p>
            </div>
        </div>

        <div class="mt-5 grid grid-cols-1 gap-2 text-sm font-semibold sm:grid-cols-3">
            <div class="flex min-w-[8rem] flex-1 items-center justify-center rounded-full border border-ui-border/80 bg-ui-surface/85 px-3 py-2 text-center text-ui-anchor/85 shadow-sm shadow-ui-anchor/5 ring-1 ring-ui-border/70 sm:flex-none">
                <span class="min-w-0 truncate">Claim Validation Proof</span>
            </div>
            <div class="flex min-w-[8rem] flex-1 items-center justify-center rounded-full bg-ui-proof/10 px-3 py-2 text-center text-ui-proof ring-1 ring-ui-proof/15 sm:flex-none">
                <span class="min-w-0 truncate">Settlement Proof</span>
            </div>
            <div class="flex min-w-[8rem] flex-1 items-center justify-center rounded-full bg-ui-success/10 px-3 py-2 text-center text-ui-success ring-1 ring-ui-success/15 sm:flex-none">
                <span class="min-w-0 truncate">EDUX Transfer Layer</span>
            </div>
        </div>
    </section>

    <x-table-card
        title="Recent Blockchain Verification Records"
        description="Latest proof logs created from claim processing activity.">
        <table class="min-w-[46rem] divide-y divide-ui-border text-sm lg:min-w-full">
            <thead class="bg-ui-canvas/70">
                <tr>
                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Type</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Reference</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Proof Hash</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Status</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Recorded</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-ui-border/70 bg-ui-surface">
                @forelse($recentTransactions as $transaction)
                    <tr class="transition hover:bg-ui-canvas/70">
                        <td class="px-5 py-3.5 font-semibold text-ui-text">
                            {{ $transaction->transaction_type }}
                        </td>

                        <td class="px-5 py-3.5 font-mono text-xs text-ui-subtext">
                            {{ $transaction->reference_code ?? 'N/A' }}
                        </td>

                        <td class="max-w-[16rem] break-all px-5 py-3.5 font-mono text-xs text-ui-subtext xl:max-w-[18rem]">
                            {{ $transaction->transaction_hash ?? 'Pending' }}
                        </td>

                        <td class="px-5 py-3.5">
                            <x-status-badge
                                :status="$transaction->blockchain_status"
                                :tone="$transaction->blockchain_status === 'Confirmed' ? 'confirmed' : 'warning'" />
                        </td>

                        <td class="px-5 py-3.5 text-ui-subtext">
                            {{ $transaction->recorded_at?->format('M d, Y h:i A') ?? 'Not recorded' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <p class="font-semibold text-ui-text">No verification records yet</p>
                            <p class="mt-2 text-sm text-ui-subtext">Morph proof records will appear after merchant claim processing.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-card>
</div>

@endsection
