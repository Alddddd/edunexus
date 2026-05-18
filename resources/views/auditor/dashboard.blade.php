@extends('layouts.dashboard')

@section('title', 'Auditor Dashboard')

@section('content')

<div class="max-w-7xl space-y-6">
    <x-page-header
        title="Auditor Dashboard"
        eyebrow="Verification Review"
        description="Monitor claim activity and blockchain verification records for audit transparency." />

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-ui-border/90 bg-ui-surface p-5 shadow-[0_14px_32px_rgba(15,47,44,0.06)]">
            <p class="text-sm text-ui-subtext">Total Processed Claims</p>
            <p class="mt-2 text-3xl font-bold text-ui-text">{{ number_format($totalClaims) }}</p>
            <p class="mt-1 text-sm text-cyan-600">Merchant-processed assistance</p>
        </div>

        <div class="rounded-2xl border border-ui-border/90 bg-ui-surface p-5 shadow-[0_14px_32px_rgba(15,47,44,0.06)]">
            <p class="text-sm text-ui-subtext">Confirmed Proofs</p>
            <p class="mt-2 text-3xl font-bold text-ui-success">{{ number_format($confirmedProofs) }}</p>
            <p class="mt-1 text-sm text-emerald-600">Verified blockchain records</p>
        </div>

        <div class="rounded-2xl border border-ui-border/90 bg-ui-surface p-5 shadow-[0_14px_32px_rgba(15,47,44,0.06)]">
            <p class="text-sm text-ui-subtext">Pending Proofs</p>
            <p class="mt-2 text-3xl font-bold text-ui-warning">{{ number_format($pendingProofs) }}</p>
            <p class="mt-1 text-sm text-amber-600">Awaiting confirmation</p>
        </div>
    </div>

    <x-table-card
        title="Recent Blockchain Verification Records"
        description="Latest proof logs created from claim processing activity.">
        <table class="min-w-full divide-y divide-ui-border text-sm">
            <thead class="bg-ui-canvas/70">
                <tr>
                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Type</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Reference</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Hash</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Status</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Recorded</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-ui-border/70 bg-ui-surface">
                @forelse($recentTransactions as $transaction)
                    <tr class="transition hover:bg-ui-canvas/70">
                        <td class="px-5 py-4 font-semibold text-ui-text">
                            {{ $transaction->transaction_type }}
                        </td>

                        <td class="px-5 py-4 font-mono text-xs text-ui-subtext">
                            {{ $transaction->reference_code ?? 'N/A' }}
                        </td>

                        <td class="max-w-[18rem] break-all px-5 py-4 font-mono text-xs text-ui-subtext">
                            {{ $transaction->transaction_hash ?? 'Pending' }}
                        </td>

                        <td class="px-5 py-4">
                            <x-status-badge
                                :status="$transaction->blockchain_status"
                                :tone="$transaction->blockchain_status === 'Confirmed' ? 'confirmed' : 'warning'" />
                        </td>

                        <td class="px-5 py-4 text-ui-subtext">
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
