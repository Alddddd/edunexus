@extends('layouts.dashboard')

@section('title', 'Claim Verification')

@section('content')

@php
    $allRulesPassed = collect($rules)->every(fn ($rule) => $rule['passed']);
    $passedRules = collect($rules)->where('passed', true)->count();
    $failedRules = collect($rules)->where('passed', false)->count();
@endphp

<div class="w-full min-w-0 max-w-6xl space-y-6">
    <x-page-header
        title="Programmable Rule Review"
        eyebrow="Claim Verification Result"
        description="Review eligibility checks before processing merchant settlement and recording Morph proof.">
        <x-slot:actions>
            <a href="{{ route('merchant.claims.index') }}"
               class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-ui-border bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-ui-canvas sm:w-auto">
                Validate Another Claim
            </a>
        </x-slot:actions>
    </x-page-header>

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
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5">
            <p class="font-semibold text-rose-800">
                {{ session('error') }}
            </p>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-success bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-ui-subtext">Rule Checks Passed</p>
            <p class="mt-2 text-3xl font-bold text-ui-success">{{ $passedRules }}</p>
            <p class="mt-1 text-sm text-emerald-600">Eligible validation checks</p>
        </div>

        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-danger bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-ui-subtext">Rule Checks Failed</p>
            <p class="mt-2 text-3xl font-bold {{ $failedRules > 0 ? 'text-ui-danger' : 'text-ui-text' }}">{{ $failedRules }}</p>
            <p class="mt-1 text-sm {{ $failedRules > 0 ? 'text-rose-600' : 'text-ui-subtext' }}">Blocking conditions</p>
        </div>

        <div class="min-w-0 rounded-2xl border border-t-4 border-ui-border/80 border-t-ui-action bg-ui-surface/95 p-6 shadow-[0_16px_38px_rgba(15,47,44,0.07)] ring-1 ring-ui-anchor/5">
            <p class="text-sm text-ui-subtext">Claim Amount</p>
            <p class="mt-2 text-3xl font-bold text-ui-text">&#8369;{{ number_format($request->approved_amount, 2) }}</p>
            <p class="mt-1 text-sm text-teal-600">Merchant reimbursement value</p>
        </div>
    </div>

    <x-form-card
        title="Verified Claim"
        description="Reference {{ $request->reference_code }} / approved amount PHP {{ number_format($request->approved_amount, 2) }}">
        <x-slot:actions>
            <div class="flex flex-wrap gap-2">
                <x-status-badge :status="$request->status" :tone="$request->status === 'Approved' ? 'success' : 'danger'" />
                <x-status-badge :status="$request->is_claimed ? 'Claimed' : 'Unclaimed'" :tone="$request->is_claimed ? 'claimed' : 'neutral'" />
                <x-status-badge status="Rule Engine" tone="proof" />
            </div>
        </x-slot:actions>

        <div class="grid grid-cols-1 gap-4 border-b border-ui-border/80 pb-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl bg-ui-canvas/70 p-4">
                <p class="text-sm text-ui-subtext">Member</p>
                <p class="mt-1 font-semibold text-ui-text">{{ $request->member->name }}</p>
            </div>

            <div class="rounded-xl bg-ui-canvas/70 p-4">
                <p class="text-sm text-ui-subtext">Program</p>
                <p class="mt-1 font-semibold text-ui-text">{{ $request->program->program_name }}</p>
            </div>

            <div class="rounded-xl bg-ui-canvas/70 p-4">
                <p class="text-sm text-ui-subtext">Reference Code</p>
                <p class="mt-1 break-all font-mono text-xs font-semibold text-ui-text">{{ $request->reference_code }}</p>
            </div>

            <div class="rounded-xl bg-ui-canvas/70 p-4">
                <p class="text-sm text-ui-subtext">Expiration</p>
                <p class="mt-1 font-semibold text-ui-text">{{ $request->expiration_date?->format('M d, Y') ?? 'Not available' }}</p>
            </div>
        </div>

        <div class="border-b border-ui-border/80 py-6">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-ui-text">
                        Programmable Validation Rules
                    </p>

                    <p class="mt-1 text-sm leading-6 text-ui-subtext">
                        EduNexUs evaluates each rule before allowing merchant claim processing.
                    </p>
                </div>

                <x-status-badge status="Governance Checks" tone="proof" />
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                @foreach($rules as $rule)
                    <div class="rounded-2xl border {{ $rule['passed'] ? 'border-emerald-200 bg-emerald-50' : 'border-rose-200 bg-rose-50' }} px-4 py-4">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $rule['passed'] ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                <x-icon :name="$rule['passed'] ? 'check' : 'x'" size="h-4 w-4" />
                            </div>

                            <div class="min-w-0">
                                <div class="mb-1">
                                    <x-status-badge :status="$rule['passed'] ? 'Passed' : 'Failed'" :tone="$rule['passed'] ? 'success' : 'danger'" size="xs" />
                                </div>

                                <p class="font-semibold {{ $rule['passed'] ? 'text-emerald-800' : 'text-rose-800' }}">
                                    {{ $rule['label'] }}
                                </p>

                                <p class="mt-1 text-sm leading-6 {{ $rule['passed'] ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $rule['description'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="pt-6">
            @if($request->status !== 'Approved')
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5">
                    <p class="font-semibold text-rose-700">Claim Invalid</p>
                    <p class="mt-1 text-sm text-rose-600">This request is not approved for merchant validation.</p>
                </div>
            @elseif($request->is_claimed)
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                    <p class="font-semibold text-amber-700">Claim Already Used</p>
                    <p class="mt-1 text-sm text-amber-600">This assistance claim has already been processed and cannot be used again.</p>
                </div>
            @elseif(now()->greaterThan($request->expiration_date))
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5">
                    <p class="font-semibold text-rose-700">Claim Expired</p>
                    <p class="mt-1 text-sm text-rose-600">The claim validity period has expired.</p>
                </div>
            @elseif(! $allRulesPassed)
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-6">
                    <p class="text-lg font-semibold text-rose-800">Claim Not Eligible</p>
                    <p class="mt-1 max-w-2xl text-sm text-rose-700">
                        This claim cannot be processed because one or more programmable validation rules failed.
                    </p>
                </div>
            @else
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-lg font-semibold text-emerald-800">
                                Claim Eligible
                            </p>

                            <p class="mt-1 text-sm text-emerald-700">
                                All programmable validation checks passed.
                            </p>

                            <p class="mt-4 max-w-2xl text-sm leading-6 text-emerald-700">
                                Processing this claim will mark the assistance as used, create a pending merchant settlement, notify the member, and record proof on Morph.
                            </p>
                        </div>

                        <x-status-badge status="Ready for Morph Proof" tone="success" />
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-3 text-sm md:grid-cols-3">
                        <div class="rounded-xl border border-emerald-100 bg-white p-4">
                            <p class="text-slate-500">Claim State</p>
                            <p class="mt-1 font-semibold text-slate-800">Validated</p>
                        </div>

                        <div class="rounded-xl border border-emerald-100 bg-white p-4">
                            <p class="text-slate-500">Settlement</p>
                            <p class="mt-1 font-semibold text-slate-800">Will be created</p>
                        </div>

                        <div class="rounded-xl border border-emerald-100 bg-white p-4">
                            <p class="text-slate-500">Proof Layer</p>
                            <p class="mt-1 font-semibold text-slate-800">Morph blockchain</p>
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
                                class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-ui-action px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-ui-anchor disabled:cursor-not-allowed disabled:opacity-70 md:w-auto">
                            Process Claim & Record Proof
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </x-form-card>
</div>

@endsection
