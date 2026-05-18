@extends('layouts.dashboard')

@section('title', 'Claim Verification')

@section('content')

@php
    $allRulesPassed = collect($rules)->every(fn ($rule) => $rule['passed']);
    $passedRules = collect($rules)->where('passed', true)->count();
    $failedRules = collect($rules)->where('passed', false)->count();
@endphp

<div class="max-w-6xl space-y-6">

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-teal-700">
                    Claim Verification Result
                </p>

                <h1 class="mt-2 text-3xl font-bold text-slate-800">
                    Programmable Rule Review
                </h1>

                <p class="mt-2 max-w-3xl text-slate-500">
                    Review eligibility checks before processing merchant settlement and recording Morph proof.
                </p>
            </div>

            <a href="{{ route('merchant.claims.index') }}"
               class="inline-flex w-fit items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                Validate Another Claim
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">
            <p class="font-semibold text-emerald-800">
                {{ session('success') }}
            </p>

            <p class="mt-1 text-sm text-emerald-700">
                The claim status has been updated. Check the Morph Verification Console for the blockchain proof record.
            </p>
        </div>
    @endif

    @if(session('error'))
        <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
            <p class="font-semibold text-red-800">
                {{ session('error') }}
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Rule Checks Passed
            </p>

            <p class="mt-2 text-3xl font-bold text-emerald-700">
                {{ $passedRules }}
            </p>

            <p class="mt-1 text-sm text-emerald-600">
                Eligible validation checks
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Rule Checks Failed
            </p>

            <p class="mt-2 text-3xl font-bold {{ $failedRules > 0 ? 'text-red-700' : 'text-slate-800' }}">
                {{ $failedRules }}
            </p>

            <p class="mt-1 text-sm {{ $failedRules > 0 ? 'text-red-600' : 'text-slate-400' }}">
                Blocking conditions
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-sm text-slate-500">
                Claim Amount
            </p>

            <p class="mt-2 text-3xl font-bold text-slate-800">
                PHP {{ number_format($request->approved_amount, 2) }}
            </p>

            <p class="mt-1 text-sm text-teal-600">
                Merchant reimbursement value
            </p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 p-8">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-slate-500">
                        Verified Claim
                    </p>

                    <h2 class="mt-2 text-2xl font-bold text-slate-800">
                        {{ $request->program->program_name }}
                    </h2>

                    <p class="mt-2 text-slate-500">
                        Reference {{ $request->reference_code }} · PHP {{ number_format($request->approved_amount, 2) }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full px-3 py-1 text-xs font-semibold
                        {{ $request->status === 'Approved'
                            ? 'bg-emerald-100 text-emerald-700'
                            : 'bg-red-100 text-red-700' }}">
                        {{ $request->status }}
                    </span>

                    <span class="rounded-full px-3 py-1 text-xs font-semibold
                        {{ $request->is_claimed
                            ? 'bg-cyan-100 text-cyan-700'
                            : 'bg-slate-100 text-slate-600' }}">
                        {{ $request->is_claimed ? 'Claimed' : 'Unclaimed' }}
                    </span>

                    <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">
                        Rule Engine
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 border-b border-slate-100 p-8 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                <p class="text-sm text-slate-500">
                    Member
                </p>

                <p class="mt-1 font-semibold text-slate-800">
                    {{ $request->member->name }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                <p class="text-sm text-slate-500">
                    Program
                </p>

                <p class="mt-1 font-semibold text-slate-800">
                    {{ $request->program->program_name }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                <p class="text-sm text-slate-500">
                    Reference Code
                </p>

                <p class="mt-1 font-mono text-xs font-semibold text-slate-800">
                    {{ $request->reference_code }}
                </p>
            </div>

            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                <p class="text-sm text-slate-500">
                    Expiration
                </p>

                <p class="mt-1 font-semibold text-slate-800">
                    {{ $request->expiration_date?->format('M d, Y') ?? 'Not available' }}
                </p>
            </div>
        </div>

        <div class="border-b border-slate-100 p-8">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-800">
                        Programmable Validation Rules
                    </p>

                    <p class="mt-1 text-sm text-slate-500">
                        EduNexUs evaluates each rule before allowing merchant claim processing.
                    </p>
                </div>

                <span class="rounded-full bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700">
                    Governance Checks
                </span>
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                @foreach($rules as $rule)
                    <div class="rounded-2xl border {{ $rule['passed'] ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50' }} px-4 py-4">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold {{ $rule['passed'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $rule['passed'] ? 'OK' : '!' }}
                            </div>

                            <div>
                                <p class="font-semibold {{ $rule['passed'] ? 'text-emerald-800' : 'text-red-800' }}">
                                    {{ $rule['label'] }}
                                </p>

                                <p class="mt-1 text-sm {{ $rule['passed'] ? 'text-emerald-700' : 'text-red-700' }}">
                                    {{ $rule['description'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="p-8">
            @if($request->status !== 'Approved')
                <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
                    <p class="font-semibold text-red-700">
                        Claim Invalid
                    </p>

                    <p class="mt-1 text-sm text-red-600">
                        This request is not approved for merchant validation.
                    </p>
                </div>
            @elseif($request->is_claimed)
                <div class="rounded-2xl border border-yellow-200 bg-yellow-50 p-5">
                    <p class="font-semibold text-yellow-700">
                        Claim Already Used
                    </p>

                    <p class="mt-1 text-sm text-yellow-600">
                        This assistance claim has already been processed and cannot be used again.
                    </p>
                </div>
            @elseif(now()->greaterThan($request->expiration_date))
                <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
                    <p class="font-semibold text-red-700">
                        Claim Expired
                    </p>

                    <p class="mt-1 text-sm text-red-600">
                        The claim validity period has expired.
                    </p>
                </div>
            @elseif(! $allRulesPassed)
                <div class="rounded-2xl border border-red-200 bg-red-50 p-6">
                    <p class="text-lg font-semibold text-red-800">
                        Claim Not Eligible
                    </p>

                    <p class="mt-1 max-w-2xl text-sm text-red-700">
                        This claim cannot be processed because one or more programmable validation rules failed.
                    </p>
                </div>
            @else
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-lg font-semibold text-emerald-800">
                                Claim Eligible
                            </p>

                            <p class="mt-1 text-sm text-emerald-700">
                                All programmable validation checks passed.
                            </p>

                            <p class="mt-4 max-w-2xl text-sm text-emerald-700">
                                Processing this claim will mark the assistance as used, create a pending merchant settlement, notify the member, and record proof on Morph.
                            </p>
                        </div>

                        <span class="rounded-full border border-emerald-200 bg-white px-3 py-1 text-xs font-semibold text-emerald-700">
                            Ready for Morph Proof
                        </span>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-3 text-sm md:grid-cols-3">
                        <div class="rounded-xl border border-emerald-100 bg-white p-4">
                            <p class="text-slate-500">
                                Claim State
                            </p>

                            <p class="mt-1 font-semibold text-slate-800">
                                Validated
                            </p>
                        </div>

                        <div class="rounded-xl border border-emerald-100 bg-white p-4">
                            <p class="text-slate-500">
                                Settlement
                            </p>

                            <p class="mt-1 font-semibold text-slate-800">
                                Will be created
                            </p>
                        </div>

                        <div class="rounded-xl border border-emerald-100 bg-white p-4">
                            <p class="text-slate-500">
                                Proof Layer
                            </p>

                            <p class="mt-1 font-semibold text-slate-800">
                                Morph blockchain
                            </p>
                        </div>
                    </div>

                    <form method="POST"
                          action="{{ route('merchant.claims.process', $request) }}"
                          class="mt-6"
                          data-confirm
                          data-confirm-title="Process claim and record proof?"
                          data-confirm-message="This will mark the claim as used, create a pending settlement, notify the member, and record proof on Morph."
                          data-confirm-button="Process claim"
                          data-confirm-tone="success"
                          data-loading-text="Recording proof on Morph..."
                          data-loader-title="Recording claim proof..."
                          data-loader-message="Processing the claim, preparing settlement, and recording operational proof on Morph.">
                        @csrf

                        <button type="submit"
                                class="w-full rounded-xl bg-teal-600 px-6 py-3 font-semibold text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-70 md:w-auto">
                            Process Claim & Record Proof
                        </button>
                    </form>
                </div>
            @endif
        </div>

    </div>

</div>

@endsection
