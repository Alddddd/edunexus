@extends('layouts.dashboard')

@section('title', 'Assistance Requests')

@section('content')
<div class="w-full min-w-0 max-w-7xl space-y-6">
    <x-page-header
        title="Assistance Requests"
        eyebrow="Admin Operations"
        description="Review member assistance applications before approval, QR generation, merchant validation, and Morph proof recording." />

    <section class="rounded-2xl border border-ui-border bg-ui-surface p-4 shadow-sm shadow-slate-200/60 sm:p-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-ui-text">
                    Workflow Progress
                </p>

                <p class="mt-1 text-sm leading-6 text-ui-subtext">
                    Current queue stage for admin review before claim pass validation.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 text-sm text-slate-600">
                <x-status-badge status="Request" tone="neutral" />
                <x-icon name="chevron-right" size="hidden h-4 w-4 text-slate-400 sm:block" />
                <x-status-badge status="Approval" tone="warning" />
                <x-icon name="chevron-right" size="hidden h-4 w-4 text-slate-400 sm:block" />
                <x-status-badge status="QR Generated" tone="success" />
                <x-icon name="chevron-right" size="hidden h-4 w-4 text-slate-400 sm:block" />
                <x-status-badge status="Merchant Claim" tone="proof" />
                <x-icon name="chevron-right" size="hidden h-4 w-4 text-slate-400 sm:block" />
                <x-status-badge status="Morph Proof" tone="proof" />
            </div>
        </div>
    </section>

    <x-table-card
        title="Request Queue"
        description="Newest applications appear first. Open a request to approve, reject, or inspect its workflow details.">
        <div class="hidden md:block">
            <table class="min-w-full divide-y divide-ui-border text-sm">
                <thead class="bg-ui-canvas/70">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Member</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Program</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Requested Amount</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Status</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Date</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-ui-subtext">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-ui-border/70 bg-ui-surface">
                    @forelse($requests as $request)
                        <tr class="transition hover:bg-ui-canvas/60">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-ui-text">
                                    {{ $request->member->name }}
                                </p>
                            </td>

                            <td class="px-5 py-4 text-slate-600">
                                {{ $request->program->program_name }}
                            </td>

                            <td class="px-5 py-4 font-semibold text-ui-text">
                                &#8369;{{ number_format($request->requested_amount, 2) }}
                            </td>

                            <td class="px-5 py-4">
                                <x-status-badge
                                    :status="$request->is_claimed ? 'Claimed' : $request->status"
                                    :tone="$request->is_claimed ? 'claimed' : $request->status" />
                            </td>

                            <td class="px-5 py-4 text-ui-subtext">
                                {{ $request->created_at->format('M d, Y') }}
                            </td>

                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.assistance-requests.show', $request) }}"
                                   class="inline-flex min-h-10 items-center justify-center rounded-xl px-3 py-2 text-sm font-semibold text-ui-action transition hover:bg-teal-50 hover:text-ui-anchor">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <p class="font-medium text-ui-text">
                                    No assistance requests submitted yet.
                                </p>

                                <p class="mt-1 text-sm text-ui-subtext">
                                    New member applications will appear here for admin review.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-ui-border/80 md:hidden">
            @forelse($requests as $request)
                <article class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-semibold text-ui-text">
                                {{ $request->member->name }}
                            </p>

                            <p class="mt-1 text-sm leading-5 text-ui-subtext">
                                {{ $request->program->program_name }}
                            </p>
                        </div>

                        <x-status-badge
                            :status="$request->is_claimed ? 'Claimed' : $request->status"
                            :tone="$request->is_claimed ? 'claimed' : $request->status" />
                    </div>

                    <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-xl bg-ui-canvas/70 p-3">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext">
                                Amount
                            </dt>
                            <dd class="mt-1 font-semibold text-ui-text">
                                &#8369;{{ number_format($request->requested_amount, 2) }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-ui-canvas/70 p-3">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext">
                                Date
                            </dt>
                            <dd class="mt-1 font-semibold text-ui-text">
                                {{ $request->created_at->format('M d, Y') }}
                            </dd>
                        </div>
                    </dl>

                    <a href="{{ route('admin.assistance-requests.show', $request) }}"
                       class="mt-4 inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-ui-action px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor">
                        Review Request
                    </a>
                </article>
            @empty
                <div class="px-4 py-10 text-center">
                    <p class="font-medium text-ui-text">
                        No assistance requests submitted yet.
                    </p>

                    <p class="mt-1 text-sm text-ui-subtext">
                        New member applications will appear here for admin review.
                    </p>
                </div>
            @endforelse
        </div>

        <x-slot:footer>
            <div class="flex flex-col items-center justify-center gap-3 text-center">
                <p class="text-sm text-ui-subtext">
                    Showing {{ $requests->firstItem() ?? 0 }} to {{ $requests->lastItem() ?? 0 }} of {{ $requests->total() }} requests
                </p>

                <div class="flex max-w-full justify-center overflow-x-auto">
                    {{ $requests->links() }}
                </div>
            </div>
        </x-slot:footer>
    </x-table-card>
</div>
@endsection
