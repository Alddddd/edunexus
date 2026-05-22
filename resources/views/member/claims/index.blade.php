@extends('layouts.dashboard')

@section('title', 'My Claims')

@section('content')

@php
    $totalClaims = $claimStats['total'];
    $pendingClaims = $claimStats['pending'];
    $activeClaimPasses = $claimStats['active'];
    $claimedRequests = $claimStats['claimed'];

    $statusLabel = fn ($claim) => $claim->is_claimed ? 'Claimed' : $claim->status;
    $statusTone = fn ($claim) => $claim->is_claimed ? 'claimed' : $claim->status;
@endphp

<div class="w-full min-w-0 max-w-7xl space-y-6 text-ui-anchor">
    <x-page-header
        title="My Assistance Claims"
        eyebrow="Claim Tracking"
        description="Track submitted assistance requests, approved claim passes, and merchant redemption status.">
        <x-slot:actions>
            <a href="{{ route('member.assistance-requests.create') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-5 py-2.5 text-sm font-semibold text-white shadow-[0_10px_20px_rgba(11,93,86,0.18)] transition hover:bg-ui-anchor">
                Request Assistance
            </a>

            <a href="{{ route('member.dashboard') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-xl border border-ui-border/80 bg-ui-surface/70 px-5 py-2.5 text-sm font-semibold text-ui-anchor/85 shadow-sm shadow-ui-anchor/5 transition hover:border-ui-action/25 hover:bg-ui-surface">
                Member Dashboard
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-action bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-slate-500">
                Total Requests
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-800">
                {{ number_format($totalClaims) }}
            </p>

            <p class="mt-1 text-sm text-slate-400">
                Submitted assistance records
            </p>
        </div>

        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-warning bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-slate-500">
                Pending Review
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-800">
                {{ number_format($pendingClaims) }}
            </p>

            <p class="mt-1 text-sm text-amber-600">
                Waiting for cooperative approval
            </p>
        </div>

        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-success bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-slate-500">
                Active Claim Passes
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-800">
                {{ number_format($activeClaimPasses) }}
            </p>

            <p class="mt-1 text-sm text-emerald-600">
                Ready for merchant validation
            </p>
        </div>

        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-proof bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-slate-500">
                Claimed
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-800">
                {{ number_format($claimedRequests) }}
            </p>

            <p class="mt-1 text-sm text-cyan-600">
                Processed by merchant
            </p>
        </div>
    </div>

    <div class="w-full min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-4 py-5 sm:px-6">
            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                <div class="min-w-0">
                    <h2 class="text-lg font-semibold text-slate-800">
                        Claim Passes
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Approved requests show a QR/reference code that can be presented to an accredited merchant.
                    </p>
                </div>

                <span class="inline-flex w-fit rounded-full border border-teal-200 bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">
                    QR validation ready after approval
                </span>
            </div>
        </div>

        <div class="hidden max-w-full overflow-x-auto md:block">
            <table class="min-w-[58rem] divide-y divide-slate-200 text-sm lg:min-w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Program
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Reference
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Amount
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Expiration
                        </th>
                        <th class="w-[18rem] px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($claims as $claim)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-6 py-5 align-top">
                                <p class="font-semibold text-slate-800">
                                    {{ $claim->program->program_name }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Assistance request
                                </p>
                            </td>

                            <td class="px-6 py-5 align-top">
                                <p class="font-mono text-xs font-semibold text-slate-700">
                                    {{ $claim->reference_code ?? 'Pending approval' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $claim->qr_code ? 'QR pass available' : 'QR after approval' }}
                                </p>
                            </td>

                            <td class="px-6 py-5 align-top">
                                <p class="font-semibold text-slate-800">
                                    ₱{{ number_format($claim->approved_amount ?? $claim->requested_amount, 2) }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $claim->approved_amount ? 'Approved amount' : 'Requested amount' }}
                                </p>
                            </td>

                            <td class="px-6 py-5 align-top">
                                <x-status-badge :status="$statusLabel($claim)" :tone="$statusTone($claim)" />
                            </td>

                            <td class="px-6 py-5 align-top">
                                <p class="text-sm font-medium text-slate-700">
                                    {{ $claim->expiration_date?->format('M d, Y') ?? 'Not available' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $claim->expiration_date ? 'Claim validity' : 'Set after approval' }}
                                </p>
                            </td>

                            <td class="px-6 py-5 align-top">
                                <div class="flex flex-wrap items-center gap-2">
                                    @if($claim->status === 'Pending' && ! $claim->is_claimed)
                                        <a href="{{ route('member.assistance-requests.edit', $claim) }}"
                                           class="inline-flex h-11 w-28 flex-none items-center justify-center rounded-xl bg-ui-anchor px-4 text-xs font-semibold text-white shadow-sm transition hover:!bg-ui-action hover:text-white">
                                            Edit
                                        </a>

                                        <form method="POST"
                                            action="{{ route('member.assistance-requests.destroy', $claim) }}"
                                            class="w-28 flex-none"
                                            data-confirm
                                            data-confirm-title="Cancel this pending request?"
                                            data-confirm-message="This will delete the pending request before admin review."
                                            data-confirm-button="Cancel request"
                                            data-confirm-tone="danger"
                                            data-loading-text="Cancelling request..."
                                            data-loader-title="Cancelling request..."
                                            data-loader-message="Removing the pending request from the review queue.">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="inline-flex h-11 w-full items-center justify-center rounded-xl bg-ui-danger px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-rose-700">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('member.claims.show', $claim) }}"
                                       class="inline-flex h-11 w-28 flex-none items-center justify-center rounded-xl bg-ui-action px-4 text-xs font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
                                        View Claim
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="mx-auto max-w-md">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                        <x-icon name="qr-code" size="h-8 w-8" />
                                    </div>

                                    <h3 class="mt-5 text-lg font-semibold text-slate-700">
                                        No assistance claims yet
                                    </h3>

                                    <p class="mt-2 text-sm text-slate-500">
                                        Submit an assistance request first. Approved requests will generate a QR/reference claim pass.
                                    </p>

                                    <a href="{{ route('member.assistance-requests.create') }}"
                                       class="mt-5 inline-flex rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-700">
                                        Request Assistance
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-slate-100 md:hidden">
            @forelse($claims as $claim)
                <article class="p-4">
                    <div class="flex flex-col gap-3">
                        <div class="flex min-w-0 flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="break-words font-semibold text-slate-800">
                                    {{ $claim->program->program_name }}
                                </p>

                                <p class="mt-1 break-all font-mono text-xs font-semibold text-slate-500">
                                    {{ $claim->reference_code ?? 'Pending approval' }}
                                </p>
                            </div>

                            <x-status-badge class="shrink-0" :status="$statusLabel($claim)" :tone="$statusTone($claim)" />
                        </div>

                        <div class="grid grid-cols-1 gap-3 rounded-2xl bg-slate-50 p-3 text-sm">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Amount</p>
                                <p class="mt-1 font-semibold text-slate-800">
                                    ₱{{ number_format($claim->approved_amount ?? $claim->requested_amount, 2) }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Expiration</p>
                                <p class="mt-1 font-medium text-slate-700">
                                    {{ $claim->expiration_date?->format('M d, Y') ?? 'Not available' }}
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                            @if($claim->status === 'Pending' && ! $claim->is_claimed)
                                <a href="{{ route('member.assistance-requests.edit', $claim) }}"
                                   class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-anchor px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:!bg-ui-action hover:text-white">
                                    Edit
                                </a>

                                <form method="POST"
                                      action="{{ route('member.assistance-requests.destroy', $claim) }}"
                                      data-confirm
                                      data-confirm-title="Cancel this pending request?"
                                      data-confirm-message="This will delete the pending request before admin review."
                                      data-confirm-button="Cancel request"
                                      data-confirm-tone="danger"
                                      data-loading-text="Cancelling request..."
                                      data-loader-title="Cancelling request..."
                                      data-loader-message="Removing the pending request from the review queue.">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-ui-danger px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-rose-700">
                                        Cancel
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('member.claims.show', $claim) }}"
                               class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
                                View Claim
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="px-4 py-14 text-center">
                    <div class="mx-auto max-w-md">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                            <x-icon name="qr-code" size="h-8 w-8" />
                        </div>

                        <h3 class="mt-5 text-lg font-semibold text-slate-700">
                            No assistance claims yet
                        </h3>

                        <p class="mt-2 text-sm text-slate-500">
                            Submit an assistance request first. Approved requests will generate a QR/reference claim pass.
                        </p>

                        <a href="{{ route('member.assistance-requests.create') }}"
                           class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-700">
                            Request Assistance
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="border-t border-slate-100 bg-slate-50 px-4 py-4 sm:px-6">
            <div class="flex flex-col items-center justify-center gap-3 text-center">
                <p class="text-sm text-slate-500">
                    Showing {{ $claims->firstItem() ?? 0 }} to {{ $claims->lastItem() ?? 0 }} of {{ $claims->total() }} claims
                </p>

                <div class="max-w-full overflow-x-auto">
                    {{ $claims->links() }}
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
