@extends('layouts.dashboard')

@section('title', 'Activity Timeline')

@section('content')

@php
    $hasFilters = filled($filters['status'] ?? null) || filled($filters['event_type'] ?? null);

    $formatEventType = fn ($eventType) => str($eventType)
        ->replace('_', ' ')
        ->title();

    $eventClasses = function ($eventType) {
        return match ($eventType) {
            'request_approved' => 'bg-teal-100 text-teal-700',
            'request_rejected' => 'bg-rose-100 text-rose-700',
            'claim_processed' => 'bg-indigo-100 text-indigo-700',
            'blockchain_confirmed' => 'bg-cyan-100 text-cyan-700',
            'settlement_completed' => 'bg-emerald-100 text-emerald-700',
            default => 'bg-slate-100 text-slate-600',
        };
    };

    $eventIcons = function ($eventType) {
        return match ($eventType) {
            'request_approved' => 'check-circle',
            'request_rejected' => 'x-circle',
            'claim_processed' => 'shield-check',
            'blockchain_confirmed' => 'link',
            'settlement_completed' => 'credit-card',
            default => 'activity',
        };
    };
@endphp

<div class="w-full min-w-0 max-w-7xl space-y-6">

    <x-page-header
        title="Activity Timeline"
        eyebrow="Audit Visibility"
        description="Operational audit trail for approvals, claims, blockchain validation, and settlement activity.">
        <x-slot:actions>
            <div class="metric-current-view rounded-2xl border px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-ui-action">
                    Current View
                </p>

                <p class="mt-1 text-2xl font-bold text-ui-anchor">
                    {{ number_format($activities->total()) }}
                </p>

                <p class="text-xs text-ui-subtext">
                    {{ $hasFilters ? 'Filtered audit records' : 'All audit records' }}
                </p>
            </div>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-slate-500">
                Total Events
            </p>

            <p class="mt-2 text-2xl font-bold text-slate-800">
                {{ number_format($stats['total']) }}
            </p>

            <p class="mt-1 text-xs text-slate-400">
                Complete operational log
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-slate-500">
                Request Decisions
            </p>

            <p class="mt-2 text-2xl font-bold text-teal-700">
                {{ number_format($stats['approvals']) }}
            </p>

            <p class="mt-1 text-xs text-teal-600">
                Approved and rejected requests
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-slate-500">
                Claim Proof Events
            </p>

            <p class="mt-2 text-2xl font-bold text-indigo-700">
                {{ number_format($stats['claims']) }}
            </p>

            <p class="mt-1 text-xs text-indigo-600">
                Merchant validation activity
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-slate-500">
                Settlements
            </p>

            <p class="mt-2 text-2xl font-bold text-emerald-700">
                {{ number_format($stats['settlements']) }}
            </p>

            <p class="mt-1 text-xs text-emerald-600">
                Completed reimbursement events
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-slate-500">
                Needs Review
            </p>

            <p class="mt-2 text-2xl font-bold text-rose-700">
                {{ number_format($stats['attention']) }}
            </p>

            <p class="mt-1 text-xs text-rose-600">
                Failed or rejected records
            </p>
        </div>

    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">
                    Filter Audit Records
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Narrow the timeline by workflow event or operational status while preserving the audit record order.
                </p>
            </div>

            @if($hasFilters)
                <a href="{{ route('admin.activity-logs.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Clear filters
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-[1fr_1fr_auto]">
            <div>
                <label for="event_type" class="block text-sm font-semibold text-slate-700">
                    Event Type
                </label>

                <select id="event_type"
                        name="event_type"
                        class="mt-2 w-full rounded-xl border-slate-200 text-sm text-slate-700 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    <option value="">All event types</option>

                    @foreach($eventTypeOptions as $eventType)
                        <option value="{{ $eventType }}" @selected(($filters['event_type'] ?? null) === $eventType)>
                            {{ $formatEventType($eventType) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-semibold text-slate-700">
                    Status
                </label>

                <select id="status"
                        name="status"
                        class="mt-2 w-full rounded-xl border-slate-200 text-sm text-slate-700 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                    <option value="">All statuses</option>

                    @foreach($statusOptions as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? null) === $status)>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-ui-action/20 bg-ui-action/10 px-5 py-2.5 text-sm font-semibold text-ui-action shadow-sm transition hover:bg-ui-action/15 lg:w-auto">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-6 py-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">
                        Operational Timeline
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Showing {{ $activities->firstItem() ?? 0 }} to {{ $activities->lastItem() ?? 0 }} of {{ $activities->total() }} records.
                    </p>
                </div>

                <span class="inline-flex w-fit rounded-full border border-teal-200 bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">
                    Audit console
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-slate-200">

                <thead class="bg-slate-50">
                    <tr>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Event
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Description
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Status
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Actor
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Recorded
                        </th>

                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 bg-white">

                    @forelse($activities as $log)

                        <tr class="transition hover:bg-slate-50">

                            <td class="px-6 py-5 align-top">

                                <div class="flex items-start gap-3">

                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $eventClasses($log->event_type) }}">
                                        <x-icon :name="$eventIcons($log->event_type)" size="h-5 w-5" />
                                    </div>

                                    <div>
                                        <p class="font-semibold text-slate-800">
                                            {{ $log->title }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $formatEventType($log->event_type) }}
                                        </p>
                                    </div>

                                </div>

                            </td>

                            <td class="px-6 py-5 align-top">

                                <p class="max-w-xl text-sm leading-relaxed text-slate-600">
                                    {{ $log->description ?? 'No additional details recorded.' }}
                                </p>

                                @if($log->reference_type || $log->reference_id)
                                    <p class="mt-2 text-xs text-slate-400">
                                        Reference #{{ $log->reference_id ?? 'N/A' }}
                                    </p>
                                @endif

                            </td>

                            <td class="px-6 py-5 align-top">

                                <x-status-badge :status="$log->status ?? 'Recorded'" :tone="$log->status ?? 'neutral'" />

                            </td>

                            <td class="px-6 py-5 align-top">

                                <p class="text-sm font-medium text-slate-700">
                                    {{ $log->user->name ?? 'System' }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $log->user?->role ? ucfirst($log->user->role) : 'Automated record' }}
                                </p>

                            </td>

                            <td class="px-6 py-5 align-top">

                                <p class="text-sm font-medium text-slate-700">
                                    {{ $log->created_at->format('M d, Y') }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $log->created_at->format('g:i A') }}
                                </p>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">

                                <div class="mx-auto max-w-md">

                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                        <x-icon name="activity" size="h-8 w-8" />
                                    </div>

                                    <h3 class="mt-5 text-lg font-semibold text-slate-700">
                                        No activity records found
                                    </h3>

                                    <p class="mt-2 text-sm text-slate-500">
                                        {{ $hasFilters
                                            ? 'No audit records match the selected filters. Clear filters to return to the full timeline.'
                                            : 'Operational events will appear here once admins, merchants, and blockchain workflows begin processing.' }}
                                    </p>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="border-t border-slate-100 bg-slate-50 px-6 py-4">
            <div class="flex flex-col items-center justify-center gap-3 text-center">
                <p class="text-sm text-slate-500">
                    Page {{ $activities->currentPage() }} of {{ $activities->lastPage() }}
                </p>

                <div class="flex justify-center">
                    {{ $activities->links() }}
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
