@extends('layouts.dashboard')

@section('title', 'Claim Verification')

@section('content')

@php
    $allRulesPassed = collect($rules)->every(fn ($rule) => $rule['passed']);
    $passedRules = collect($rules)->where('passed', true)->count();
    $failedRules = collect($rules)->where('passed', false)->count();
    $totalRules = count($rules);
    $validationTitle = $allRulesPassed ? 'Eligible for Claim Processing' : 'Claim Not Eligible';
    $validationMessage = $allRulesPassed
        ? $passedRules . ' of ' . $totalRules . ' governance checks passed.'
        : $failedRules . ' of ' . $totalRules . ' governance ' . str($failedRules === 1 ? 'check requires' : 'checks require') . ' attention.';
    $validationToneClasses = $allRulesPassed
        ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
        : 'border-rose-200 bg-rose-50 text-rose-800';
    $validationIconClasses = $allRulesPassed
        ? 'bg-emerald-100 text-emerald-700 ring-emerald-200'
        : 'bg-rose-100 text-rose-700 ring-rose-200';
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

    <section class="validation-overview rounded-2xl border p-6 shadow-[0_18px_42px_rgba(15,47,44,0.08)] ring-1 ring-ui-anchor/5 {{ $validationToneClasses }}">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex min-w-0 items-start gap-4">
                <div class="validation-icon flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl ring-1 {{ $validationIconClasses }}">
                    <x-icon :name="$allRulesPassed ? 'shield-check' : 'alert-triangle'" size="h-6 w-6" />
                </div>

                <div class="min-w-0">
                    <p class="text-sm font-semibold uppercase tracking-wider {{ $allRulesPassed ? 'text-emerald-700' : 'text-rose-700' }}">
                        Programmable Validation Result
                    </p>

                    <h2 class="mt-1 text-2xl font-bold">
                        {{ $validationTitle }}
                    </h2>

                    <p class="mt-2 text-sm leading-6 {{ $allRulesPassed ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $validationMessage }}
                    </p>
                </div>
            </div>

            <x-status-badge
                :status="$allRulesPassed ? 'Ready to Process' : 'Processing Blocked'"
                :tone="$allRulesPassed ? 'success' : 'danger'" />
        </div>

        <div class="mt-6 grid grid-cols-1 gap-3 text-sm md:grid-cols-3">
            <div class="rounded-xl border border-white/70 bg-white/70 p-4">
                <p class="{{ $allRulesPassed ? 'text-emerald-700' : 'text-rose-700' }}">Checks Passed</p>
                <p class="mt-1 text-2xl font-bold">{{ $passedRules }}</p>
            </div>

            <div class="rounded-xl border border-white/70 bg-white/70 p-4">
                <p class="{{ $allRulesPassed ? 'text-emerald-700' : 'text-rose-700' }}">Needs Attention</p>
                <p class="mt-1 text-2xl font-bold">{{ $failedRules }}</p>
            </div>

            <div class="rounded-xl border border-white/70 bg-white/70 p-4">
                <p class="{{ $allRulesPassed ? 'text-emerald-700' : 'text-rose-700' }}">Claim Amount</p>
                <p class="mt-1 text-2xl font-bold">&#8369;{{ number_format($request->approved_amount, 2) }}</p>
            </div>
        </div>
    </section>

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
                    <p class="text-base font-semibold text-ui-text">
                        Governance Checks
                    </p>

                    <p class="mt-1 text-sm leading-6 text-ui-subtext">
                        Each check explains why this claim can proceed or why processing is blocked.
                    </p>
                </div>

                <x-status-badge status="Governance Checks" tone="proof" />
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                @foreach($rules as $rule)
                    <div class="validation-rule-card rounded-2xl border px-4 py-4 {{ $rule['passed'] ? 'border-emerald-200 bg-emerald-50/90 hover:border-emerald-300' : 'border-rose-200 bg-rose-50/90 hover:border-rose-300' }}">
                        <div class="flex items-start gap-3">
                            <div class="validation-icon mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full ring-1 {{ $rule['passed'] ? 'bg-emerald-100 text-emerald-700 ring-emerald-200' : 'bg-rose-100 text-rose-700 ring-rose-200' }}">
                                <x-icon :name="$rule['passed'] ? 'check-circle' : 'x-circle'" size="h-5 w-5" />
                            </div>

                            <div class="min-w-0">
                                <div class="mb-2">
                                    <x-status-badge :status="$rule['passed'] ? 'Passed' : 'Failed'" :tone="$rule['passed'] ? 'success' : 'danger'" size="xs" />
                                </div>

                                <p class="font-semibold {{ $rule['passed'] ? 'text-emerald-800' : 'text-rose-800' }}">
                                    {{ $rule['label'] }}
                                </p>

                                <p class="mt-1 text-sm leading-6 {{ $rule['passed'] ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $rule['message'] }}
                                </p>

                                <p class="mt-3 font-mono text-[10px] uppercase tracking-wider {{ $rule['passed'] ? 'text-emerald-600/70' : 'text-rose-600/70' }}">
                                    Audit rule: {{ str($rule['key'])->replace('_', ' ') }}
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
                    <p class="font-semibold text-rose-700">Claim Validity Expired</p>
                    <p class="mt-1 text-sm text-rose-600">This claim pass is outside its approved redemption period.</p>
                </div>
            @elseif(! $allRulesPassed)
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-6">
                    <p class="text-lg font-semibold text-rose-800">Claim Not Eligible</p>
                    <p class="mt-1 max-w-2xl text-sm text-rose-700">
                        This claim cannot be processed because one or more programmable validation rules failed.
                    </p>

                    <div class="mt-4 space-y-2">
                        @foreach(collect($rules)->where('passed', false) as $rule)
                            <div class="rounded-xl border border-rose-100 bg-white/70 px-4 py-3 text-sm text-rose-700">
                                <span class="font-semibold">{{ $rule['label'] }}:</span>
                                {{ $rule['message'] }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-lg font-semibold text-emerald-800">
                                Claim Eligible
                            </p>

                            <p class="mt-1 text-sm text-emerald-700">
                                Eligible for claim processing.
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
