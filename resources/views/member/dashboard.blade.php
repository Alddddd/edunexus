@extends('layouts.dashboard')

@section('title', 'Member Dashboard')

@section('content')

@php
    $statusClasses = function ($request) {
        if ($request?->is_claimed) {
            return 'bg-cyan-100 text-cyan-700';
        }

        return match ($request?->status) {
            'Approved' => 'bg-emerald-100 text-emerald-700',
            'Rejected' => 'bg-red-100 text-red-700',
            default => 'bg-yellow-100 text-yellow-700',
        };
    };

    $statusLabel = function ($request) {
        return $request?->is_claimed ? 'Claimed' : ($request?->status ?? 'No request yet');
    };
@endphp

<div class="max-w-7xl space-y-6">

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-teal-700">
                    Member Portal
                </p>

                <h1 class="mt-2 text-3xl font-bold text-slate-800">
                    Welcome to EduNexUs
                </h1>

                <p class="mt-2 max-w-3xl text-slate-500">
                    Submit assistance requests, track approval status, and access your QR claim pass once approved.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="{{ route('member.assistance-requests.create') }}"
                   class="inline-flex items-center justify-center rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-700">
                    Request Assistance
                </a>

                <a href="{{ route('member.claims.index') }}"
                   class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    My Claims
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Total Requests
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-800">
                {{ number_format($totalRequests) }}
            </p>

            <p class="mt-1 text-sm text-slate-400">
                Submitted assistance requests
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Pending Review
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-800">
                {{ number_format($pendingRequests) }}
            </p>

            <p class="mt-1 text-sm text-yellow-600">
                Waiting for cooperative approval
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Active Claim Passes
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-800">
                {{ number_format($approvedClaimPasses) }}
            </p>

            <p class="mt-1 text-sm text-emerald-600">
                Ready for merchant validation
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Claimed Assistance
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-800">
                {{ number_format($claimedRequests) }}
            </p>

            <p class="mt-1 text-sm text-cyan-600">
                Processed by merchant
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">
                        Latest Request Status
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Your most recent assistance request and next step.
                    </p>
                </div>

                @if($latestRequest)
                    <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses($latestRequest) }}">
                        {{ $statusLabel($latestRequest) }}
                    </span>
                @endif
            </div>

            @if($latestRequest)
                <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Program
                        </p>

                        <p class="mt-2 font-semibold text-slate-800">
                            {{ $latestRequest->program->program_name ?? 'Assistance program' }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Amount
                        </p>

                        <p class="mt-2 font-semibold text-slate-800">
                            PHP {{ number_format($latestRequest->approved_amount ?? $latestRequest->requested_amount, 2) }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Reference
                        </p>

                        <p class="mt-2 font-mono text-xs font-semibold text-slate-800">
                            {{ $latestRequest->reference_code ?? 'Pending approval' }}
                        </p>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl border border-teal-100 bg-teal-50 p-5">
                    @if($latestRequest->is_claimed)
                        <p class="font-semibold text-cyan-800">
                            Claim processed
                        </p>

                        <p class="mt-1 text-sm text-cyan-700">
                            This assistance has already been validated and processed by a merchant.
                        </p>
                    @elseif($latestRequest->status === 'Approved')
                        <p class="font-semibold text-teal-800">
                            Claim pass ready
                        </p>

                        <p class="mt-1 text-sm text-teal-700">
                            Present your QR/reference claim pass to an accredited merchant for validation.
                        </p>
                    @elseif($latestRequest->status === 'Rejected')
                        <p class="font-semibold text-red-800">
                            Request was not approved
                        </p>

                        <p class="mt-1 text-sm text-red-700">
                            You may submit a new request for another available assistance program.
                        </p>
                    @else
                        <p class="font-semibold text-yellow-800">
                            Awaiting cooperative review
                        </p>

                        <p class="mt-1 text-sm text-yellow-700">
                            Your request is in the admin review queue. You will receive a claim pass after approval.
                        </p>
                    @endif
                </div>
            @else
                <div class="mt-6 rounded-2xl border border-slate-100 bg-slate-50 p-8 text-center">
                    <p class="font-semibold text-slate-700">
                        No assistance request yet
                    </p>

                    <p class="mt-2 text-sm text-slate-500">
                        Start by submitting a request for an available cooperative assistance program.
                    </p>

                    <a href="{{ route('member.assistance-requests.create') }}"
                       class="mt-5 inline-flex rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-700">
                        Request Assistance
                    </a>
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800">
                Claim Pass Access
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Approved requests generate a QR/reference pass for merchant validation.
            </p>

            @if($latestApprovedClaimPass)
                <div class="mt-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
                    <p class="font-semibold text-emerald-800">
                        Approved claim pass available
                    </p>

                    <p class="mt-2 text-sm text-emerald-700">
                        {{ $latestApprovedClaimPass->program->program_name ?? 'Assistance program' }}
                    </p>

                    <p class="mt-3 font-mono text-xs font-semibold text-emerald-900">
                        {{ $latestApprovedClaimPass->reference_code ?? 'Reference pending' }}
                    </p>
                </div>

                <a href="{{ route('member.claims.show', $latestApprovedClaimPass) }}"
                   class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-700">
                    Open Claim Pass
                </a>
            @else
                <div class="mt-5 rounded-2xl border border-slate-100 bg-slate-50 p-5">
                    <p class="font-semibold text-slate-700">
                        No active claim pass
                    </p>

                    <p class="mt-2 text-sm text-slate-500">
                        Your QR/reference pass will appear after an assistance request is approved.
                    </p>
                </div>

                <a href="{{ route('member.claims.index') }}"
                   class="mt-5 inline-flex w-full items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    View My Claims
                </a>
            @endif
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-5">
            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">
                        Recent Requests and Claims
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Track your latest assistance activity and claim pass status.
                    </p>
                </div>

                <a href="{{ route('member.claims.index') }}"
                   class="text-sm font-semibold text-teal-600 hover:text-teal-700">
                    View all
                </a>
            </div>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($recentClaims as $claim)
                <div class="px-6 py-4 transition hover:bg-slate-50">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="font-semibold text-slate-800">
                                {{ $claim->program->program_name ?? 'Assistance program' }}
                            </p>

                            <p class="mt-1 font-mono text-xs text-slate-400">
                                {{ $claim->reference_code ?? 'Pending reference' }}
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 md:justify-end">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses($claim) }}">
                                {{ $statusLabel($claim) }}
                            </span>

                            <p class="text-sm font-semibold text-slate-800">
                                PHP {{ number_format($claim->approved_amount ?? $claim->requested_amount, 2) }}
                            </p>

                            <a href="{{ route('member.claims.show', $claim) }}"
                               class="text-sm font-semibold text-teal-600 hover:text-teal-700">
                                View
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <p class="font-semibold text-slate-700">
                        No request activity yet
                    </p>

                    <p class="mt-2 text-sm text-slate-500">
                        Submitted requests and claim passes will appear here.
                    </p>
                </div>
            @endforelse
        </div>
    </div>

</div>

@endsection
