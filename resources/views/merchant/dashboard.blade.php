@extends('layouts.dashboard')

@section('title', 'Merchant Dashboard')

@section('content')

@php
    $profileStatusClasses = ($merchantProfile?->status ?? 'Inactive') === 'Active'
        ? 'bg-emerald-100 text-emerald-700'
        : 'bg-red-100 text-red-700';

    $settlementStatusClasses = function ($status) {
        return $status === 'Settled'
            ? 'bg-emerald-100 text-emerald-700'
            : 'bg-yellow-100 text-yellow-700';
    };
@endphp

<div class="max-w-7xl space-y-6">

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-teal-700">
                    Merchant Claim Terminal
                </p>

                <h1 class="mt-2 text-3xl font-bold text-slate-800">
                    Merchant Dashboard
                </h1>

                <p class="mt-2 max-w-3xl text-slate-500">
                    Validate member claim passes, process eligible assistance claims, and monitor cooperative settlement status.
                </p>
            </div>

            <a href="{{ route('merchant.claims.index') }}"
               class="inline-flex w-fit items-center justify-center rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-700">
                Validate Claim
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Processed Claims
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-800">
                {{ number_format($processedClaims) }}
            </p>

            <p class="mt-1 text-sm text-cyan-600">
                Validated claim transactions
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Pending Settlements
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-800">
                {{ number_format($pendingSettlements) }}
            </p>

            <p class="mt-1 text-sm text-yellow-600">
                Awaiting cooperative reimbursement
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Settled Records
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-800">
                {{ number_format($settledSettlements) }}
            </p>

            <p class="mt-1 text-sm text-emerald-600">
                Reimbursed by cooperative
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Settlement Value
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-800">
                PHP {{ number_format($totalSettlementValue, 2) }}
            </p>

            <p class="mt-1 text-sm text-teal-600">
                Total merchant claim value
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">
                        Merchant Profile
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Accreditation details used during programmable claim validation.
                    </p>
                </div>

                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $profileStatusClasses }}">
                    {{ $merchantProfile->status ?? 'No profile' }}
                </span>
            </div>

            <div class="mt-5 space-y-4">
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Business
                    </p>

                    <p class="mt-2 font-semibold text-slate-800">
                        {{ $merchantProfile->business_name ?? auth()->user()->name }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Merchant Category
                    </p>

                    <p class="mt-2 font-semibold text-slate-800">
                        {{ $merchantProfile->merchant_category ?? 'Not configured' }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Pending Value
                    </p>

                    <p class="mt-2 font-semibold text-slate-800">
                        PHP {{ number_format($pendingSettlementValue, 2) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-teal-100 bg-teal-50 p-6 shadow-sm xl:col-span-2">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-teal-700">
                        Claim Processing Workflow
                    </p>

                    <h2 class="mt-2 text-2xl font-bold text-slate-800">
                        Validate, Process, Record Proof, Settle
                    </h2>

                    <p class="mt-2 max-w-3xl text-slate-600">
                        EduNexUs checks approval status, expiration, duplicate use, amount limits, and merchant category before processing a claim and recording Morph proof.
                    </p>
                </div>

                <span class="inline-flex w-fit rounded-xl border border-teal-200 bg-white px-4 py-2 text-sm font-semibold text-teal-700">
                    Rule-enforced terminal
                </span>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-3 text-sm md:grid-cols-4">
                <div class="rounded-2xl border border-white bg-white/80 p-4">
                    <p class="font-semibold text-slate-800">
                        1. Enter Reference
                    </p>

                    <p class="mt-1 text-slate-500">
                        Scan QR or type claim code.
                    </p>
                </div>

                <div class="rounded-2xl border border-white bg-white/80 p-4">
                    <p class="font-semibold text-slate-800">
                        2. Rules Execute
                    </p>

                    <p class="mt-1 text-slate-500">
                        Program and merchant checks run.
                    </p>
                </div>

                <div class="rounded-2xl border border-white bg-white/80 p-4">
                    <p class="font-semibold text-slate-800">
                        3. Process Claim
                    </p>

                    <p class="mt-1 text-slate-500">
                        Mark used and create settlement.
                    </p>
                </div>

                <div class="rounded-2xl border border-white bg-white/80 p-4">
                    <p class="font-semibold text-slate-800">
                        4. Morph Proof
                    </p>

                    <p class="mt-1 text-slate-500">
                        Record tamper-resistant proof.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-6 py-5">
            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">
                        Recent Processed Claims
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Claims processed by this merchant and their reimbursement status.
                    </p>
                </div>

                <a href="{{ route('merchant.claims.index') }}"
                   class="text-sm font-semibold text-teal-600 hover:text-teal-700">
                    Validate new claim
                </a>
            </div>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($recentSettlements as $settlement)
                <div class="px-6 py-4 transition hover:bg-slate-50">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="font-semibold text-slate-800">
                                {{ $settlement->assistanceRequest->member->name ?? 'Member' }}
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                {{ $settlement->assistanceRequest->program->program_name ?? 'Assistance program' }}
                            </p>

                            <p class="mt-2 font-mono text-xs text-slate-400">
                                {{ $settlement->assistanceRequest->reference_code ?? 'No reference' }}
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3 md:justify-end">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $settlementStatusClasses($settlement->status) }}">
                                {{ $settlement->status }}
                            </span>

                            <p class="font-semibold text-slate-800">
                                PHP {{ number_format($settlement->amount, 2) }}
                            </p>

                            <p class="text-xs text-slate-400">
                                {{ $settlement->created_at->format('M d, Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-14 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-sm font-bold text-slate-500">
                        POS
                    </div>

                    <h3 class="mt-5 text-lg font-semibold text-slate-700">
                        No processed claims yet
                    </h3>

                    <p class="mt-2 text-sm text-slate-500">
                        Validated member claims will appear here with settlement tracking.
                    </p>

                    <a href="{{ route('merchant.claims.index') }}"
                       class="mt-5 inline-flex rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-700">
                        Validate Claim
                    </a>
                </div>
            @endforelse
        </div>
    </div>

</div>

@endsection
