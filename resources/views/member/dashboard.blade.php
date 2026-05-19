@extends('layouts.dashboard')

@section('title', 'Member Dashboard')

@section('content')

@php
    $statusLabel = fn ($request) => $request?->is_claimed ? 'Claimed' : ($request?->status ?? 'No request yet');
    $statusTone = fn ($request) => $request?->is_claimed ? 'claimed' : ($request?->status ?? 'neutral');
@endphp

<div class="w-full min-w-0 max-w-7xl space-y-6 text-ui-anchor">
    <x-page-header
        title="Welcome to EduNexUs"
        eyebrow="Member Portal"
        description="Submit assistance requests, track approval status, and access your QR claim pass once approved.">
        <x-slot:actions>
            <a href="{{ route('member.assistance-requests.create') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-5 py-2.5 text-sm font-semibold text-white shadow-[0_10px_20px_rgba(11,93,86,0.18)] transition hover:bg-ui-anchor">
                Request Assistance
            </a>

            <a href="{{ route('member.claims.index') }}"
               class="inline-flex min-h-11 items-center justify-center rounded-xl border border-ui-border/80 bg-ui-surface/70 px-5 py-2.5 text-sm font-semibold text-ui-anchor/85 shadow-sm shadow-ui-anchor/5 transition hover:border-ui-action/25 hover:bg-ui-surface">
                My Claims
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-action bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-ui-subtext">Total Requests</p>
            <p class="mt-2 text-3xl font-bold text-ui-text">{{ number_format($totalRequests) }}</p>
            <p class="mt-1 text-sm text-ui-subtext">Submitted assistance requests</p>
        </div>

        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-warning bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-ui-subtext">Pending Review</p>
            <p class="mt-2 text-3xl font-bold text-ui-warning">{{ number_format($pendingRequests) }}</p>
            <p class="mt-1 text-sm text-amber-600">Waiting for cooperative approval</p>
        </div>

        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-success bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-ui-subtext">Active Claim Passes</p>
            <p class="mt-2 text-3xl font-bold text-ui-success">{{ number_format($approvedClaimPasses) }}</p>
            <p class="mt-1 text-sm text-emerald-600">Ready for merchant validation</p>
        </div>

        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-proof bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-ui-subtext">Claimed Assistance</p>
            <p class="mt-2 text-3xl font-bold text-ui-proof">{{ number_format($claimedRequests) }}</p>
            <p class="mt-1 text-sm text-cyan-600">Processed by merchant</p>
        </div>
    </div>

    <x-form-card
        title="Request Lifecycle"
        description="A compact view of where your assistance records are in the cooperative workflow.">
        <div class="mb-6 flex flex-wrap items-center gap-2 text-sm font-semibold">
            <div class="flex min-w-[8rem] flex-1 items-center gap-2 rounded-full border border-ui-border/80 bg-ui-surface/85 px-3 py-2 text-ui-anchor/85 shadow-sm shadow-ui-anchor/5 ring-1 ring-ui-border/70 sm:flex-none">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-ui-muted text-xs">1</span>
                <span class="min-w-0 truncate">Request</span>
            </div>
            <x-icon name="chevron-right" size="hidden h-4 w-4 text-ui-action/45 sm:block" />
            <div class="flex min-w-[8rem] flex-1 items-center gap-2 rounded-full bg-ui-warning/10 px-3 py-2 text-ui-warning ring-1 ring-ui-warning/15 sm:flex-none">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-ui-warning/15 text-xs">2</span>
                <span class="min-w-0 truncate">Review</span>
            </div>
            <x-icon name="chevron-right" size="hidden h-4 w-4 text-ui-action/45 sm:block" />
            <div class="flex min-w-[8rem] flex-1 items-center gap-2 rounded-full bg-ui-success/10 px-3 py-2 text-ui-success ring-1 ring-ui-success/15 sm:flex-none">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-ui-success/15 text-xs">3</span>
                <span class="min-w-0 truncate">QR Pass</span>
            </div>
            <x-icon name="chevron-right" size="hidden h-4 w-4 text-ui-action/45 sm:block" />
            <div class="flex min-w-[8rem] flex-1 items-center gap-2 rounded-full bg-ui-proof/10 px-3 py-2 text-ui-proof ring-1 ring-ui-proof/15 sm:flex-none">
                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-ui-proof/15 text-xs">4</span>
                <span class="min-w-0 truncate">Claim</span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 md:grid-cols-4">
            <div class="rounded-2xl border border-amber-100 bg-amber-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-700">Pending</p>
                <p class="mt-2 text-2xl font-bold text-amber-800">{{ number_format($pendingRequests) }}</p>
                <p class="mt-1 text-xs text-amber-700">Editable until reviewed</p>
            </div>

            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-700">Approved</p>
                <p class="mt-2 text-2xl font-bold text-emerald-800">{{ number_format($approvedRequests) }}</p>
                <p class="mt-1 text-xs text-emerald-700">QR/reference generated</p>
            </div>

            <div class="rounded-2xl border border-cyan-100 bg-cyan-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-cyan-700">Claimed</p>
                <p class="mt-2 text-2xl font-bold text-cyan-800">{{ number_format($claimedRequests) }}</p>
                <p class="mt-1 text-xs text-cyan-700">Merchant validated</p>
            </div>

            <div class="rounded-2xl border border-rose-100 bg-rose-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-rose-700">Rejected</p>
                <p class="mt-2 text-2xl font-bold text-rose-800">{{ number_format($rejectedRequests) }}</p>
                <p class="mt-1 text-xs text-rose-700">Closed after review</p>
            </div>
        </div>
    </x-form-card>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <x-form-card
            class="xl:col-span-2"
            title="Latest Request Status"
            description="Your most recent assistance request and next operational step.">
            <x-slot:actions>
                @if($latestRequest)
                    <x-status-badge :status="$statusLabel($latestRequest)" :tone="$statusTone($latestRequest)" />
                @endif
            </x-slot:actions>

            @if($latestRequest)
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="rounded-xl bg-ui-canvas/70 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-ui-subtext">Program</p>
                        <p class="mt-2 font-semibold text-ui-text">{{ $latestRequest->program->program_name ?? 'Assistance program' }}</p>
                    </div>

                    <div class="rounded-xl bg-ui-canvas/70 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-ui-subtext">Amount</p>
                        <p class="mt-2 font-semibold text-ui-text">&#8369;{{ number_format($latestRequest->approved_amount ?? $latestRequest->requested_amount, 2) }}</p>
                    </div>

                    <div class="rounded-xl bg-ui-canvas/70 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-ui-subtext">Reference</p>
                        <p class="mt-2 break-all font-mono text-xs font-semibold text-ui-text">{{ $latestRequest->reference_code ?? 'Pending approval' }}</p>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl border border-teal-100 bg-teal-50 p-5">
                    @if($latestRequest->is_claimed)
                        <p class="font-semibold text-cyan-800">Claim processed</p>
                        <p class="mt-1 text-sm text-cyan-700">This assistance has already been validated and processed by a merchant.</p>
                    @elseif($latestRequest->status === 'Approved')
                        <p class="font-semibold text-teal-800">Claim pass ready</p>
                        <p class="mt-1 text-sm text-teal-700">Present your QR/reference claim pass to an accredited merchant for validation.</p>
                    @elseif($latestRequest->status === 'Rejected')
                        <p class="font-semibold text-rose-800">Request was not approved</p>
                        <p class="mt-1 text-sm text-rose-700">You may submit a new request for another available assistance program.</p>
                    @else
                        <p class="font-semibold text-amber-800">Awaiting cooperative review</p>
                        <p class="mt-1 text-sm text-amber-700">Your request is in the admin review queue. You may still edit or cancel it while pending.</p>

                        <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('member.assistance-requests.edit', $latestRequest) }}"
                               class="inline-flex min-h-10 items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-amber-700 shadow-sm transition hover:bg-amber-100">
                                Edit Pending Request
                            </a>

                            <form method="POST"
                                  action="{{ route('member.assistance-requests.destroy', $latestRequest) }}"
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
                                        class="inline-flex min-h-10 w-full items-center justify-center rounded-xl bg-ui-danger px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700 sm:w-auto">
                                    Cancel Request
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @else
                <div class="rounded-2xl border border-ui-border bg-ui-canvas/70 p-8 text-center">
                    <p class="font-semibold text-ui-text">No assistance request yet</p>
                    <p class="mt-2 text-sm text-ui-subtext">Start by submitting a request for an available cooperative assistance program.</p>
                    <a href="{{ route('member.assistance-requests.create') }}"
                       class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-ui-action px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-ui-anchor">
                        Request Assistance
                    </a>
                </div>
            @endif
        </x-form-card>

        <x-form-card
            title="Claim Pass Access"
            description="Approved requests generate a QR/reference pass for merchant validation.">
            @if($latestApprovedClaimPass)
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                    <p class="font-semibold text-emerald-800">Approved claim pass available</p>
                    <p class="mt-2 text-sm text-emerald-700">{{ $latestApprovedClaimPass->program->program_name ?? 'Assistance program' }}</p>
                    <p class="mt-3 break-all font-mono text-xs font-semibold text-emerald-900">{{ $latestApprovedClaimPass->reference_code ?? 'Reference pending' }}</p>
                </div>

                <a href="{{ route('member.claims.show', $latestApprovedClaimPass) }}"
                   class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-ui-action px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-ui-anchor">
                    Open Claim Pass
                </a>
            @else
                <div class="rounded-2xl border border-ui-border bg-ui-canvas/70 p-5">
                    <p class="font-semibold text-ui-text">No active claim pass</p>
                    <p class="mt-2 text-sm text-ui-subtext">Your QR/reference pass will appear after an assistance request is approved.</p>
                </div>

                <a href="{{ route('member.claims.index') }}"
                   class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-ui-border px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas">
                    View My Claims
                </a>
            @endif
        </x-form-card>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <x-table-card
            class="xl:col-span-2"
            title="Recent Requests and Claims"
            description="Track your latest assistance activity and claim pass status.">
            <div class="divide-y divide-ui-border/80">
                @forelse($recentClaims as $claim)
                    <div class="px-5 py-4 transition hover:bg-ui-canvas/70">
                        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="font-semibold text-ui-text">{{ $claim->program->program_name ?? 'Assistance program' }}</p>
                                <p class="mt-1 font-mono text-xs text-ui-subtext">{{ $claim->reference_code ?? 'Pending reference' }}</p>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 md:justify-end">
                                <x-status-badge :status="$statusLabel($claim)" :tone="$statusTone($claim)" />
                                <p class="text-sm font-semibold text-ui-text">&#8369;{{ number_format($claim->approved_amount ?? $claim->requested_amount, 2) }}</p>

                                @if($claim->status === 'Pending' && ! $claim->is_claimed)
                                    <a href="{{ route('member.assistance-requests.edit', $claim) }}"
                                       class="text-sm font-semibold text-ui-action hover:text-ui-anchor">
                                        Edit
                                    </a>
                                @endif

                                <a href="{{ route('member.claims.show', $claim) }}"
                                   class="text-sm font-semibold text-ui-action hover:text-ui-anchor">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center">
                        <p class="font-semibold text-ui-text">No request activity yet</p>
                        <p class="mt-2 text-sm text-ui-subtext">Submitted requests and claim passes will appear here.</p>
                    </div>
                @endforelse
            </div>
        </x-table-card>

        <x-form-card
            title="Recent Notifications"
            description="Latest member workflow updates.">
            <div class="space-y-3">
                @forelse($recentNotifications as $notification)
                    <a href="{{ route('notifications.read', $notification->id) }}"
                       class="block rounded-xl border border-ui-border bg-ui-canvas/60 p-4 transition hover:bg-ui-canvas">
                        <div class="flex items-start justify-between gap-3">
                            <p class="font-semibold text-ui-text">{{ $notification->data['title'] ?? 'Notification' }}</p>
                            <x-status-badge :status="$notification->read_at ? 'Read' : 'Unread'" :tone="$notification->read_at ? 'neutral' : 'warning'" size="xs" />
                        </div>

                        <p class="mt-1 text-sm leading-5 text-ui-subtext">{{ $notification->data['message'] ?? 'No details available.' }}</p>
                        <p class="mt-2 text-xs text-ui-subtext">{{ $notification->created_at->diffForHumans() }}</p>
                    </a>
                @empty
                    <div class="rounded-xl border border-ui-border bg-ui-canvas/60 p-5 text-center">
                        <p class="font-semibold text-ui-text">No notifications yet</p>
                        <p class="mt-1 text-sm text-ui-subtext">Approval and claim updates will appear here.</p>
                    </div>
                @endforelse
            </div>
        </x-form-card>
    </div>
</div>

@endsection
