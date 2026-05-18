@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')

@section('content')

@php
    $statusClasses = function ($status) {
        return match ($status) {
            'Approved', 'Confirmed', 'Settled' => 'bg-emerald-100 text-emerald-700',
            'Rejected', 'Failed' => 'bg-red-100 text-red-700',
            default => 'bg-yellow-100 text-yellow-700',
        };
    };

    $latestHash = $latestBlockchainTransaction?->transaction_hash;
    $shortLatestHash = $latestHash && str_starts_with($latestHash, '0x')
        ? substr($latestHash, 0, 10) . '...' . substr($latestHash, -8)
        : ($latestHash ?? 'No hash recorded');
@endphp

<div class="max-w-7xl space-y-6">

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-teal-700">
                    Operational Command Center
                </p>

                <h1 class="mt-2 text-3xl font-bold text-slate-800">
                    EduNexUs Admin Dashboard
                </h1>

                <p class="mt-2 max-w-3xl text-slate-500">
                    Monitor assistance approvals, merchant claims, settlements, Morph proof records, and live operational activity.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.assistance-requests.index') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-700">
                    Review Requests
                </a>

                <a href="{{ route('admin.settlements.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Settlements
                </a>

                <a href="{{ route('admin.blockchain-transactions.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Morph Proofs
                </a>

                <a href="{{ route('admin.activity-logs.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Activity Timeline
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-slate-500">
                Total Approved Assistance
            </p>

            <p class="mt-3 text-3xl font-bold text-slate-800">
                PHP {{ number_format($totalApprovedAssistance, 2) }}
            </p>

            <p class="mt-2 text-sm text-emerald-600">
                Approved assistance value
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-slate-500">
                Pending Approvals
            </p>

            <p class="mt-3 text-3xl font-bold text-slate-800">
                {{ number_format($pendingRequests) }}
            </p>

            <p class="mt-2 text-sm text-yellow-600">
                Awaiting admin review
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-slate-500">
                Pending Settlement Value
            </p>

            <p class="mt-3 text-3xl font-bold text-slate-800">
                PHP {{ number_format($pendingSettlementAmount, 2) }}
            </p>

            <p class="mt-2 text-sm text-yellow-600">
                Outstanding merchant reimbursement
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm text-slate-500">
                Morph Confirmations
            </p>

            <p class="mt-3 text-3xl font-bold text-slate-800">
                {{ number_format($confirmedBlockchainLogs) }}
            </p>

            <p class="mt-2 text-sm text-cyan-600">
                Confirmed proof records
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">
                        Assistance Engine
                    </p>

                    <p class="mt-1 text-lg font-semibold text-emerald-700">
                        Operational
                    </p>
                </div>

                <span class="h-3 w-3 rounded-full bg-emerald-500"></span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">
                        Merchant Validation
                    </p>

                    <p class="mt-1 text-lg font-semibold text-cyan-700">
                        Active
                    </p>
                </div>

                <span class="h-3 w-3 rounded-full bg-cyan-500"></span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">
                        Morph Verification
                    </p>

                    <p class="mt-1 text-lg font-semibold text-teal-700">
                        Connected
                    </p>
                </div>

                <span class="h-3 w-3 rounded-full bg-teal-500"></span>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-teal-100 bg-teal-50 p-6 shadow-sm">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-teal-700">
                    Live MVP Workflow
                </p>

                <h2 class="mt-2 text-2xl font-bold text-slate-800">
                    Programmable Assistance with Morph Proof Recording
                </h2>

                <p class="mt-2 max-w-3xl text-slate-600">
                    EduNexUs validates merchant claims through programmable rules, records proof on Morph, and tracks settlement status for cooperative reimbursement.
                </p>
            </div>

            <span class="inline-flex w-fit rounded-xl border border-teal-200 bg-white px-4 py-2 text-sm font-semibold text-teal-700">
                Demo-ready workflow
            </span>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-2 text-sm text-slate-600 md:grid-cols-3 xl:grid-cols-6">
            <span class="rounded-full border border-slate-200 bg-white px-3 py-2 text-center">
                Member Request
            </span>

            <span class="rounded-full bg-yellow-100 px-3 py-2 text-center text-yellow-700">
                Admin Approval
            </span>

            <span class="rounded-full bg-emerald-100 px-3 py-2 text-center text-emerald-700">
                QR Generated
            </span>

            <span class="rounded-full bg-cyan-100 px-3 py-2 text-center text-cyan-700">
                Merchant Validates
            </span>

            <span class="rounded-full bg-teal-100 px-3 py-2 text-center text-teal-700">
                Morph Proof
            </span>

            <span class="rounded-full bg-slate-100 px-3 py-2 text-center text-slate-700">
                Settlement
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">
                        Operational Summary
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Read-only snapshot of approval, settlement, and verification health.
                    </p>
                </div>

                <span class="inline-flex w-fit rounded-full border border-teal-200 bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">
                    {{ number_format($approvalRate, 1) }}% approval rate
                </span>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Requests
                    </p>

                    <p class="mt-2 text-2xl font-bold text-slate-800">
                        {{ number_format($totalRequests) }}
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        {{ number_format($approvedRequests) }} approved, {{ number_format($rejectedRequests) }} rejected
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Claims
                    </p>

                    <p class="mt-2 text-2xl font-bold text-slate-800">
                        {{ number_format($claimedRequests) }}
                    </p>

                    <p class="mt-1 text-sm text-cyan-600">
                        Merchant processed assistance
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Settlements
                    </p>

                    <p class="mt-2 text-2xl font-bold text-slate-800">
                        {{ number_format($totalSettlements) }}
                    </p>

                    <p class="mt-1 text-sm text-emerald-600">
                        PHP {{ number_format($settledAmount, 2) }} settled
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Pending Proofs
                    </p>

                    <p class="mt-2 text-2xl font-bold text-slate-800">
                        {{ number_format($pendingBlockchainProofs) }}
                    </p>

                    <p class="mt-1 text-sm text-yellow-600">
                        Awaiting verification confirmation
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 md:col-span-2">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Top Assistance Program
                    </p>

                    <p class="mt-2 text-xl font-bold text-slate-800">
                        {{ $topProgramName }}
                    </p>

                    <p class="mt-1 text-sm text-teal-600">
                        Highest request activity
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">
                        Latest Morph Proof
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Most recent blockchain verification record.
                    </p>
                </div>
            </div>

            @if($latestBlockchainTransaction)
                <div class="mt-5 space-y-4">
                    <div>
                        <p class="text-sm text-slate-500">
                            {{ $latestBlockchainTransaction->transaction_type }} proof
                        </p>

                        <p class="mt-1 font-mono text-xs font-semibold text-slate-700">
                            {{ $latestBlockchainTransaction->reference_code ?? 'N/A' }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                        <p class="font-mono text-xs font-semibold text-slate-700" title="{{ $latestHash ?? 'No hash recorded' }}">
                            {{ $shortLatestHash }}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Transaction hash
                        </p>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses($latestBlockchainTransaction->blockchain_status) }}">
                            {{ $latestBlockchainTransaction->blockchain_status }}
                        </span>

                        <p class="text-xs text-slate-400">
                            {{ $latestBlockchainTransaction->recorded_at?->diffForHumans() ?? $latestBlockchainTransaction->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <a href="{{ route('admin.blockchain-transactions.index') }}"
                       class="inline-flex w-full items-center justify-center rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-700">
                        Open Verification Console
                    </a>
                </div>
            @else
                <div class="mt-8 rounded-2xl bg-slate-50 p-6 text-center">
                    <p class="font-semibold text-slate-700">
                        No Morph proof yet
                    </p>

                    <p class="mt-2 text-sm text-slate-500">
                        Proof records appear after merchant claim processing.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">
                            Pending Approvals
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Assistance requests waiting for review.
                        </p>
                    </div>

                    <a href="{{ route('admin.assistance-requests.index') }}"
                       class="text-sm font-semibold text-teal-600 hover:text-teal-700">
                        Review
                    </a>
                </div>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($latestPendingRequests as $request)
                    <div class="px-6 py-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-semibold text-slate-800">
                                    {{ $request->member->name ?? 'Unknown member' }}
                                </p>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $request->program->program_name ?? 'Assistance program' }}
                                </p>
                            </div>

                            <p class="shrink-0 font-semibold text-slate-800">
                                PHP {{ number_format($request->requested_amount, 2) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center">
                        <p class="font-semibold text-slate-700">
                            No pending approvals
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            The approval queue is currently clear.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-6 py-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">
                            Pending Settlements
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Merchant reimbursements awaiting completion.
                        </p>
                    </div>

                    <a href="{{ route('admin.settlements.index') }}"
                       class="text-sm font-semibold text-teal-600 hover:text-teal-700">
                        Open
                    </a>
                </div>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($latestPendingSettlements as $settlement)
                    @php
                        $merchantProfile = $settlement->merchant?->merchantProfile;
                    @endphp

                    <div class="px-6 py-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-semibold text-slate-800">
                                    {{ $merchantProfile->business_name ?? $settlement->merchant->name ?? 'Merchant account' }}
                                </p>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $settlement->assistanceRequest->reference_code ?? 'No reference' }}
                                </p>
                            </div>

                            <p class="shrink-0 font-semibold text-slate-800">
                                PHP {{ number_format($settlement->amount, 2) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center">
                        <p class="font-semibold text-slate-700">
                            No pending settlements
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Merchant reimbursement queue is currently clear.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-5">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-3">
            <div class="border-b border-slate-100 px-6 py-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800">
                            Live Operations Feed
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Recent approvals, claims, settlements, and verification activity across EduNexUs.
                        </p>
                    </div>

                    <a href="{{ route('admin.activity-logs.index') }}"
                       class="text-sm font-semibold text-teal-600 hover:text-teal-700">
                        View all
                    </a>
                </div>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentActivities as $activity)
                    <div class="px-6 py-4 transition hover:bg-slate-50">
                        <div class="flex items-start gap-4">
                            <div class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-sm font-bold {{ $statusClasses($activity->status) }}">
                                {{ $activity->status === 'Rejected' || $activity->status === 'Failed' ? '!' : 'OK' }}
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                    <div>
                                        <p class="font-semibold text-slate-800">
                                            {{ $activity->title }}
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $activity->description ?? 'No additional details.' }}
                                        </p>

                                        <p class="mt-2 text-xs text-slate-400">
                                            By {{ $activity->user->name ?? 'System' }}
                                        </p>
                                    </div>

                                    <div class="shrink-0 md:text-right">
                                        @if($activity->status)
                                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses($activity->status) }}">
                                                {{ $activity->status }}
                                            </span>
                                        @endif

                                        <p class="mt-2 text-xs text-slate-400">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center">
                        <p class="text-slate-400">
                            No operational activity recorded yet.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
            <div class="border-b border-slate-100 px-6 py-5">
                <h2 class="text-lg font-semibold text-slate-800">
                    Recent Assistance Activity
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Latest requests, approvals, claims, and assistance values.
                </p>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentRequests as $request)
                    <div class="px-6 py-4 transition hover:bg-slate-50">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-semibold text-slate-800">
                                    {{ $request->member->name ?? 'Unknown member' }}
                                </p>

                                <p class="mt-1 text-sm text-slate-500">
                                    {{ $request->program->program_name ?? 'Assistance program' }}
                                </p>

                                <p class="mt-2 font-mono text-xs text-slate-400">
                                    {{ $request->reference_code ?? 'Pending reference' }}
                                </p>
                            </div>

                            <div class="shrink-0 text-right">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                    {{ $request->is_claimed
                                        ? 'bg-cyan-100 text-cyan-700'
                                        : $statusClasses($request->status) }}">
                                    {{ $request->is_claimed ? 'Claimed' : $request->status }}
                                </span>

                                <p class="mt-2 text-sm font-semibold text-slate-800">
                                    PHP {{ number_format($request->approved_amount ?? $request->requested_amount, 2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center">
                        <p class="text-slate-400">
                            No assistance activity yet.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

@endsection
