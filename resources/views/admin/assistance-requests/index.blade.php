@extends('layouts.dashboard')

@section('title', 'Assistance Requests')

@section('content')
<div class="w-full min-w-0 max-w-7xl space-y-5">
    <x-page-header
        title="Assistance Requests"
        eyebrow="Admin Operations"
        description="Review member assistance applications before approval, QR generation, merchant validation, and Morph proof recording." />

    <section class="rounded-2xl border border-ui-border/80 bg-ui-surface px-5 py-4 shadow-sm shadow-ui-anchor/5 ring-1 ring-white/70">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-bold text-ui-anchor">
                    Workflow Progress
                </p>

                <p class="mt-0.5 text-xs leading-5 text-ui-subtext/70">
                    Current queue stage for admin review before claim pass validation.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 text-sm text-ui-subtext">
                @foreach([
                    ['Request', 'neutral'],
                    ['Approval', 'warning'],
                    ['QR Generated', 'success'],
                    ['Merchant Claim', 'proof'],
                    ['Morph Proof', 'proof'],
                ] as [$label, $tone])
                    <x-status-badge :status="$label" :tone="$tone" />

                    @if(! $loop->last)
                        <x-icon name="chevron-right" size="hidden h-4 w-4 text-ui-border sm:block" />
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <x-table-card
        title="Request Queue"
        description="Newest applications first. Open a request to approve, reject, or inspect workflow details.">
        <div class="hidden md:block">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-ui-border/60 bg-gradient-to-r from-ui-canvas to-ui-muted/40">
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-ui-subtext/70">Member</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-ui-subtext/70">Program</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-ui-subtext/70">Requested</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-ui-subtext/70">Approved</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-ui-subtext/70">Status</th>
                        <th class="px-5 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-ui-subtext/70">Date</th>
                        <th class="px-5 py-3.5 text-right text-[11px] font-bold uppercase tracking-wider text-ui-subtext/70">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-ui-border/50">
                    @forelse($requests as $request)
                        <tr class="transition-colors hover:bg-ui-canvas/60">
                            <td class="px-5 py-3.5">
                                <div class="flex min-w-0 items-center gap-2.5">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-ui-action/10 text-xs font-bold text-ui-action ring-1 ring-ui-action/15">
                                        {{ strtoupper(substr($request->member->name, 0, 1)) }}
                                    </div>

                                    <span class="truncate font-semibold text-ui-anchor">
                                        {{ $request->member->name }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-5 py-3.5 text-ui-subtext">
                                {{ $request->program->program_name }}
                            </td>

                            <td class="px-5 py-3.5 font-semibold text-ui-anchor">
                                &#8369;{{ number_format($request->requested_amount, 2) }}
                            </td>

                            <td class="px-5 py-3.5">
                                @if($request->approved_amount)
                                    <span class="font-semibold text-ui-success">
                                        &#8369;{{ number_format($request->approved_amount, 2) }}
                                    </span>
                                @else
                                    <span class="text-xs text-ui-subtext/60">
                                        Pending
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-3.5">
                                <x-status-badge
                                    :status="$request->is_claimed ? 'Claimed' : $request->status"
                                    :tone="$request->is_claimed ? 'claimed' : $request->status" />
                            </td>

                            <td class="px-5 py-3.5 text-xs text-ui-subtext">
                                {{ $request->created_at->format('M d, Y') }}
                            </td>

                            <td class="px-5 py-3.5 text-right">
                                <a href="{{ route('admin.assistance-requests.show', $request) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg border border-ui-action/25 bg-ui-action/10 px-3 py-1.5 text-xs font-semibold text-ui-action transition hover:bg-ui-action hover:text-white">
                                    Review
                                    <x-icon name="chevron-right" size="h-3 w-3" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-ui-muted text-ui-subtext/50">
                                    <x-icon name="file-text" size="h-6 w-6" stroke="1.5" />
                                </div>

                                <p class="font-semibold text-ui-text">
                                    No requests yet
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

        <div class="divide-y divide-ui-border/60 md:hidden">
            @forelse($requests as $request)
                <article class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex min-w-0 items-center gap-2.5">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-ui-action/10 text-xs font-bold text-ui-action ring-1 ring-ui-action/15">
                                {{ strtoupper(substr($request->member->name, 0, 1)) }}
                            </div>

                            <div class="min-w-0">
                                <p class="truncate font-semibold text-ui-anchor">
                                    {{ $request->member->name }}
                                </p>

                                <p class="truncate text-xs text-ui-subtext">
                                    {{ $request->program->program_name }}
                                </p>
                            </div>
                        </div>

                        <x-status-badge
                            :status="$request->is_claimed ? 'Claimed' : $request->status"
                            :tone="$request->is_claimed ? 'claimed' : $request->status" />
                    </div>

                    <dl class="mt-3 grid grid-cols-2 gap-2 text-sm">
                        <div class="rounded-xl bg-ui-canvas/70 px-3 py-2.5">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext/60">
                                Requested
                            </dt>
                            <dd class="mt-0.5 font-semibold text-ui-anchor">
                                &#8369;{{ number_format($request->requested_amount, 2) }}
                            </dd>
                        </div>

                        <div class="rounded-xl bg-ui-canvas/70 px-3 py-2.5">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext/60">
                                Approved
                            </dt>
                            <dd class="mt-0.5 font-semibold {{ $request->approved_amount ? 'text-ui-success' : 'text-ui-subtext/60' }}">
                                @if($request->approved_amount)
                                    &#8369;{{ number_format($request->approved_amount, 2) }}
                                @else
                                    Pending
                                @endif
                            </dd>
                        </div>

                        <div class="rounded-xl bg-ui-canvas/70 px-3 py-2.5">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext/60">
                                Date
                            </dt>
                            <dd class="mt-0.5 font-semibold text-ui-anchor">
                                {{ $request->created_at->format('M d, Y') }}
                            </dd>
                        </div>
                    </dl>

                    <a href="{{ route('admin.assistance-requests.show', $request) }}"
                       class="mt-3 inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-ui-action px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-ui-anchor">
                        Review Request
                        <x-icon name="chevron-right" size="h-3.5 w-3.5" />
                    </a>
                </article>
            @empty
                <div class="px-4 py-12 text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-2xl bg-ui-muted text-ui-subtext/50">
                        <x-icon name="file-text" size="h-6 w-6" stroke="1.5" />
                    </div>

                    <p class="font-semibold text-ui-text">
                        No requests yet
                    </p>

                    <p class="mt-1 text-sm text-ui-subtext">
                        New member applications will appear here.
                    </p>
                </div>
            @endforelse
        </div>

        <x-slot:footer>
            <div class="flex flex-col items-center justify-center gap-3 text-center">
                <p class="text-sm text-ui-subtext">
                    Showing {{ $requests->firstItem() ?? 0 }}-{{ $requests->lastItem() ?? 0 }} of {{ $requests->total() }} requests
                </p>

                <div class="flex max-w-full justify-center overflow-x-auto">
                    {{ $requests->links() }}
                </div>
            </div>
        </x-slot:footer>
    </x-table-card>
</div>
@endsection
