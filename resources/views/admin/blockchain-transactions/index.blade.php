@extends('layouts.dashboard')

@section('title', 'Morph Verification Console')

@section('content')

@php
    $hasFilters = filled($filters['blockchain_status'] ?? null) || filled($filters['transaction_type'] ?? null) || filled($filters['search'] ?? null);
    $explorerBaseUrl = 'https://explorer-hoodi.morph.network/tx/';

    $statusTone = function ($status) {
        return match ($status) {
            'Confirmed' => 'success',
            'Failed' => 'danger',
            default => 'warning',
        };
    };

    $typeTone = function ($type) {
        return match ($type) {
            'Approval' => 'success',
            'Settlement' => 'proof',
            default => 'neutral',
        };
    };

    $typeIcons = function ($type) {
        return match ($type) {
            'Approval' => 'check-circle',
            'Settlement' => 'credit-card',
            default => 'link',
        };
    };

    $settlementLabel = function ($settlement) {
        return match ($settlement?->status) {
            'Released', 'Settled' => 'Settlement Released',
            'Partially Released' => 'Partial Payout Released',
            'Pending' => 'Settlement Generated',
            default => 'No settlement record',
        };
    };

    $settlementTone = function ($settlement) {
        return match ($settlement?->status) {
            'Released', 'Settled' => 'success',
            'Partially Released' => 'proof',
            'Pending' => 'warning',
            default => 'neutral',
        };
    };

    $integrityLabel = fn ($proofHash, $payload = []) => $proofHash
        ? 'Integrity Valid'
        : (filled($payload) ? 'Verification Metadata Unavailable' : 'Legacy Proof Record');
    $recordedLabel = fn ($transaction) => $transaction->recorded_at ? 'Proof Recorded' : 'Pending Timestamp';
    $demoSafeNotice = 'Demo-safe payout layer: PHP/GCash disbursement is simulated to avoid requiring paid payout APIs or real-money transfers during judging. Settlement proof is still recorded through the Morph rail, with real EDUX Settlement Token metadata shown when enabled.';
    $eduxLabel = fn (array $metadata) => match ($metadata['edux_transfer_status'] ?? 'skipped') {
        'success' => 'Real EDUX transfer',
        'failed' => 'EDUX transfer failed safely',
        default => 'EDUX transfer disabled for this payout',
    };
    $eduxTone = fn (array $metadata) => match ($metadata['edux_transfer_status'] ?? 'skipped') {
        'success' => 'success',
        'failed' => 'danger',
        default => 'neutral',
    };
@endphp

<div class="w-full min-w-0 max-w-7xl space-y-6">
    <x-page-header
        title="Morph Verification Console"
        eyebrow="Morph Proof Layer"
        description="Monitor Morph blockchain proof records generated from cooperative assistance validation and settlement workflows.">
        <x-slot:actions>
            <div class="metric-current-view rounded-2xl border px-5 py-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-ui-proof">
                    Current View
                </p>

                <p class="mt-1 text-2xl font-bold text-ui-anchor">
                    {{ number_format($transactions->total()) }}
                </p>

                <p class="text-xs text-ui-subtext">
                    {{ $hasFilters ? 'Filtered proof records' : 'All proof records' }}
                </p>
            </div>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Total Proof Records</p>
            <p class="mt-2 text-2xl font-bold text-ui-text">{{ number_format($stats['total']) }}</p>
            <p class="mt-1 text-xs text-ui-subtext">Blockchain verification entries</p>
        </div>

        <div class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Confirmed</p>
            <p class="mt-2 text-2xl font-bold text-ui-success">{{ number_format($stats['confirmed']) }}</p>
            <p class="mt-1 text-xs text-emerald-600">Recorded on Morph</p>
        </div>

        <div class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Pending</p>
            <p class="mt-2 text-2xl font-bold text-ui-warning">{{ number_format($stats['pending']) }}</p>
            <p class="mt-1 text-xs text-amber-600">Awaiting confirmation</p>
        </div>

        <div class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Failed</p>
            <p class="mt-2 text-2xl font-bold text-ui-danger">{{ number_format($stats['failed']) }}</p>
            <p class="mt-1 text-xs text-rose-600">Needs review</p>
        </div>

        <div class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60">
            <p class="text-sm text-ui-subtext">Integrity Hashes</p>
            <p class="mt-2 text-2xl font-bold text-ui-proof">{{ number_format($stats['with_hash']) }}</p>
            <p class="mt-1 text-xs text-cyan-600">Proof bundle hashes recorded</p>
        </div>
    </div>

    <section class="rounded-2xl border border-cyan-100 bg-cyan-50 p-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-sm font-semibold text-cyan-800">
                    Blockchain Proof Layer
                </p>

                <p class="mt-1 max-w-3xl text-sm leading-6 text-cyan-700">
                    Morph records tamper-resistant proof receipts for assistance validation and settlement events, giving auditors a stable reference without exposing wallet complexity to normal users. {{ $demoSafeNotice }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <x-status-badge status="Morph Integrated" tone="proof" />
                <x-status-badge status="Audit-ready" tone="success" />
            </div>
        </div>
    </section>

    <section class="rounded-2xl border border-ui-border bg-ui-surface p-5 shadow-sm shadow-slate-200/60 sm:p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <h2 class="text-base font-semibold text-ui-text">
                    Filter Verification Records
                </h2>

                <p class="mt-1 text-sm leading-6 text-ui-subtext">
                    Narrow proof records by workflow type or blockchain status for faster audit review.
                </p>
            </div>

            @if($hasFilters)
                <a href="{{ route('admin.blockchain-transactions.index') }}"
                   class="inline-flex min-h-10 items-center justify-center rounded-xl border border-ui-border px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-ui-canvas">
                    Clear filters
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('admin.blockchain-transactions.index') }}" class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-[1.2fr_1fr_1fr_auto]">
            <div>
                <label for="search" class="block text-sm font-semibold text-slate-700">
                    Search
                </label>

                <input id="search"
                       name="search"
                       type="search"
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Reference, member, merchant, hash, or status"
                       class="mt-2 w-full rounded-xl border-slate-200 text-sm text-slate-700 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
            </div>

            <div>
                <label for="transaction_type" class="block text-sm font-semibold text-slate-700">
                    Transaction Type
                </label>

                <select id="transaction_type"
                        name="transaction_type"
                        class="mt-2 w-full rounded-xl border-slate-200 text-sm text-slate-700 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                    <option value="">All transaction types</option>

                    @foreach($transactionTypeOptions as $transactionType)
                        <option value="{{ $transactionType }}" @selected(($filters['transaction_type'] ?? null) === $transactionType)>
                            {{ $transactionType }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="blockchain_status" class="block text-sm font-semibold text-slate-700">
                    Blockchain Status
                </label>

                <select id="blockchain_status"
                        name="blockchain_status"
                        class="mt-2 w-full rounded-xl border-slate-200 text-sm text-slate-700 shadow-sm focus:border-cyan-500 focus:ring-cyan-500">
                    <option value="">All statuses</option>

                    @foreach($statusOptions as $status)
                        <option value="{{ $status }}" @selected(($filters['blockchain_status'] ?? null) === $status)>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-ui-proof/20 bg-ui-proof/10 px-5 py-2.5 text-sm font-semibold text-ui-proof shadow-sm transition hover:bg-ui-proof/15 lg:w-auto">
                    Apply Filters
                </button>
            </div>
        </form>
    </section>

    <x-table-card
        title="Verification Records"
        description="Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} records.">
        <x-slot:actions>
            <x-status-badge status="Audit-ready proofs" tone="proof" />
        </x-slot:actions>

        <div class="hidden">
            <table class="min-w-full divide-y divide-ui-border text-sm">
                <thead class="bg-ui-canvas/70">
                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Proof Type</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Reference</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Traceability</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Governance State</th>
                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase tracking-wider text-ui-subtext">Recorded</th>
                        <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wider text-ui-subtext">Proof</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-ui-border/70 bg-ui-surface">
                    @forelse($transactions as $transaction)
                        @php
                            $hash = $transaction->transaction_hash;
                            $hasRealHash = filled($hash) && preg_match('/^0x[a-fA-F0-9]{64}$/', (string) $hash);
                            $shortHash = $hasRealHash
                                ? substr($hash, 0, 10) . '...' . substr($hash, -8)
                                : ($hash ?? 'Not available');
                            $payload = $payloads[$transaction->id] ?? [];
                            $assistanceRequest = $assistanceRequests[$transaction->reference_id] ?? null;
                            $settlement = $settlements[$transaction->reference_id] ?? null;
                            $merchantId = $payload['merchant_id'] ?? data_get($payload, 'proof_bundle.merchant_id');
                            $merchant = $merchantId ? ($merchants[$merchantId] ?? null) : null;
                            $merchantProfile = $merchant?->merchantProfile;
                            $proofHash = $payload['proof_hash'] ?? null;
                            $proofBundle = $payload['proof_bundle'] ?? [];
                            $validationRules = $payload['validation_rules'] ?? data_get($proofBundle, 'validation_rules', []);
                            $validationSummary = $payload['validation_summary'] ?? null;
                            $passedRules = (int) ($validationSummary['passed'] ?? collect($validationRules)->where('passed', true)->count());
                            $failedRules = (int) ($validationSummary['failed'] ?? collect($validationRules)->where('passed', false)->count());
                            $totalRules = $passedRules + $failedRules;
                            $ruleValidationPassed = (bool) ($validationSummary['all_passed'] ?? ($totalRules > 0 && $failedRules === 0));
                            $eventType = $payload['event_type'] ?? data_get($proofBundle, 'event_type', $transaction->transaction_type);
                            $approvedAmount = $payload['claim_amount'] ?? data_get($proofBundle, 'approved_amount') ?? $assistanceRequest?->approved_amount;
                            $settlementRail = $payload['settlement_rail'] ?? data_get($proofBundle, 'settlement_rail');
                            $payoutChannel = $payload['payout_channel'] ?? data_get($proofBundle, 'payout_channel');
                            $settlementReference = $payload['settlement_reference'] ?? data_get($proofBundle, 'settlement_reference');
                            $network = $payload['network'] ?? data_get($proofBundle, 'network');
                            $linkedPayout = $settlement?->payouts?->first();
                            $eduxTransfer = ($payload['edux_transfer'] ?? data_get($proofBundle, 'edux_transfer', [])) ?: ($linkedPayout?->metadata ?? []);
                            $claimReference = $transaction->reference_code ?? $assistanceRequest?->reference_code ?? 'Pending reference';
                            $linkedSettlementReference = $settlementReference ?? $linkedPayout?->settlement_reference ?? ($settlement ? 'Settlement #' . $settlement->id : null);
                            $eduxHash = $eduxTransfer['edux_transaction_hash'] ?? null;
                            $eduxStatus = $eduxTransfer['edux_transfer_status'] ?? null;
                            $settlementReleased = $settlement && in_array($settlement->status, ['Released', 'Settled'], true);
                            $settlementPendingLabel = $settlement ? 'Awaiting Settlement Release' : 'Settlement Not Generated';
                            $settlementStageLabel = $settlementReleased ? 'Settlement Released' : $settlementPendingLabel;
                            $eduxStageLabel = ! $settlementReleased
                                ? 'Waiting for Settlement Release'
                                : (($eduxStatus === 'success' || $eduxHash) ? 'EDUX Transfer Recorded' : ($eduxStatus === 'failed' ? 'EDUX Transfer Failed' : 'EDUX Transfer Skipped'));
                            $eduxPendingCopy = ! $settlementReleased
                                ? 'Waiting for settlement release'
                                : (($eduxStatus === 'failed') ? ($eduxTransfer['edux_error'] ?? 'EDUX transfer failed safely') : (($eduxStatus === 'skipped' || blank($eduxHash)) ? 'EDUX transfer disabled for this payout' : $eduxHash));
                            $workflowSteps = [
                                ['label' => 'Request Submitted', 'state' => $assistanceRequest ? 'done' : 'pending'],
                                ['label' => $assistanceRequest?->status === 'Approved' || $assistanceRequest?->approved_amount ? 'Approved' : 'Awaiting Approval', 'state' => $assistanceRequest?->status === 'Approved' || $assistanceRequest?->approved_amount ? 'done' : 'pending'],
                                ['label' => $transaction->transaction_type === 'Claim' || $assistanceRequest?->is_claimed ? 'Merchant Validated' : 'Awaiting Merchant Validation', 'state' => $transaction->transaction_type === 'Claim' || $assistanceRequest?->is_claimed ? 'done' : 'pending'],
                                ['label' => $settlement ? 'Settlement Generated' : 'Awaiting Settlement Generation', 'state' => $settlement ? 'done' : 'pending'],
                                ['label' => $settlementStageLabel, 'state' => $settlementReleased ? 'done' : 'pending'],
                                ['label' => $eduxStageLabel, 'state' => ($eduxStatus === 'success' || $eduxHash) ? 'done' : ($eduxStatus === 'failed' ? 'failed' : 'pending')],
                                ['label' => $transaction->blockchain_status === 'Confirmed' ? 'Morph Proof Confirmed' : ($transaction->blockchain_status === 'Failed' ? 'Morph Proof Failed' : 'Morph Proof Pending'), 'state' => $transaction->blockchain_status === 'Confirmed' ? 'done' : ($transaction->blockchain_status === 'Failed' ? 'failed' : 'pending')],
                            ];
                            $primaryReferenceLabel = $transaction->transaction_type === 'Settlement'
                                ? ($linkedPayout?->settlement_reference ? 'Payout Reference' : 'Settlement Reference')
                                : 'Claim Reference';
                            $primaryReference = $transaction->transaction_type === 'Settlement'
                                ? ($linkedSettlementReference ?? 'Settlement reference pending')
                                : $claimReference;
                            $eduxCanOpen = $transaction->transaction_type === 'Settlement'
                                && filled($eduxHash)
                                && str_starts_with((string) $eduxHash, '0x');
                            $eduxLayerCopy = $transaction->transaction_type === 'Claim'
                                ? 'Generated after settlement release'
                                : $eduxPendingCopy;
                        @endphp

                        <tr class="transition hover:bg-ui-canvas/60">
                            <td class="px-5 py-5 align-top">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-ui-canvas text-ui-action ring-1 ring-ui-border">
                                        <x-icon :name="$typeIcons($transaction->transaction_type)" size="h-5 w-5" />
                                    </div>

                                    <div>
                                        <x-status-badge :status="$transaction->transaction_type" :tone="$typeTone($transaction->transaction_type)" />

                                        <p class="mt-2 text-xs text-ui-subtext">
                                            {{ str($eventType)->replace('_', ' ')->title() }}
                                        </p>

                                        <div class="mt-2 flex flex-wrap gap-1.5">
                                            <x-status-badge :status="$transaction->blockchain_status === 'Confirmed' ? 'Verified' : ($transaction->blockchain_status === 'Failed' ? 'Proof Failed' : 'Pending Verification')" :tone="$statusTone($transaction->blockchain_status)" size="xs" />
                                            <x-status-badge :status="$integrityLabel($proofHash, $payload)" :tone="$proofHash ? 'success' : 'neutral'" size="xs" />
                                            @if($ruleValidationPassed)
                                                <x-status-badge status="Validation Passed" tone="success" size="xs" />
                                            @elseif($totalRules > 0)
                                                <x-status-badge status="Validation Review" tone="warning" size="xs" />
                                            @endif
                                            @if($settlementRail)
                                                <x-status-badge status="EDUX settlement rail" tone="proof" size="xs" />
                                            @endif
                                            @if($transaction->transaction_type === 'Settlement')
                                                <x-status-badge :status="$eduxLabel($eduxTransfer)" :tone="$eduxTone($eduxTransfer)" size="xs" />
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <div class="rounded-xl border border-ui-border bg-gradient-to-br from-white to-ui-canvas/70 px-3 py-2 shadow-sm shadow-slate-200/50">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">{{ $primaryReferenceLabel }}</p>
                                    <p class="mt-1 break-all font-mono text-sm font-bold text-ui-text">{{ $primaryReference }}</p>
                                    <p class="mt-1 text-xs text-ui-subtext">Reference #{{ $transaction->reference_id }}</p>
                                </div>

                                <p class="mt-2 text-xs text-ui-subtext">
                                    {{ $assistanceRequest?->member?->name ?? 'Member not linked' }}
                                </p>

                                <div class="mt-3 space-y-1 rounded-xl border border-ui-border bg-ui-canvas/60 px-3 py-2">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Linked Records</p>
                                    <p class="break-all font-mono text-[11px] text-ui-text">Claim Reference: {{ $claimReference }}</p>
                                    <p class="break-all font-mono text-[11px] text-ui-subtext">
                                        {{ $linkedPayout?->settlement_reference ? 'Payout Reference' : 'Linked Settlement' }}:
                                        {{ $linkedSettlementReference ?? 'Generated after payout release' }}
                                    </p>
                                    <p class="break-all font-mono text-[11px] text-ui-subtext">Linked EDUX Transfer: {{ $transaction->transaction_type === 'Settlement' ? ($eduxHash ? substr($eduxHash, 0, 12) . '...' . substr($eduxHash, -10) : $eduxPendingCopy) : 'Generated after settlement release' }}</p>
                                </div>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <div class="inline-flex max-w-xs flex-col rounded-xl border border-ui-border bg-ui-canvas/70 px-3 py-2">
                                    <span class="break-all font-mono text-xs font-semibold text-ui-text" title="{{ $hash ?? 'No transaction hash recorded' }}">
                                        {{ $shortHash }}
                                    </span>

                                    <span class="mt-1 text-xs text-ui-subtext">
                                        {{ $hasRealHash ? 'Morph transaction hash' : 'No explorer hash available' }}
                                    </span>
                                </div>

                                @if($proofHash)
                                    <div class="mt-2 inline-flex max-w-xs flex-col rounded-xl border border-cyan-100 bg-cyan-50 px-3 py-2">
                                        <span class="text-xs font-semibold text-cyan-800">Proof Bundle Hash</span>
                                        <span class="mt-1 break-all font-mono text-[11px] text-cyan-700" title="{{ $proofHash }}">
                                            {{ substr($proofHash, 0, 16) }}...{{ substr($proofHash, -12) }}
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <td class="px-5 py-5 align-top">
                                <div class="space-y-2">
                                    <x-status-badge :status="$transaction->blockchain_status" :tone="$statusTone($transaction->blockchain_status)" />
                                    <x-status-badge :status="$settlementLabel($settlement)" :tone="$settlementTone($settlement)" size="xs" />
                                    <p class="text-xs text-ui-subtext">
                                        {{ $totalRules > 0 ? $passedRules . ' of ' . $totalRules . ' governance checks passed' : 'Governance summary unavailable' }}
                                    </p>
                                </div>
                            </td>

                            <td class="px-5 py-5 align-top">
                                <p class="text-sm font-medium text-slate-700">
                                    {{ $transaction->recorded_at?->format('M d, Y') ?? 'Not recorded' }}
                                </p>

                                <p class="mt-1 text-xs text-ui-subtext">
                                    {{ $transaction->recorded_at?->format('g:i A') ?? 'Awaiting timestamp' }}
                                </p>
                            </td>

                            <td class="px-5 py-5 text-right align-top">
                                @if($hasRealHash)
                                    <a href="{{ $explorerBaseUrl . $hash }}"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       class="btn-proof min-w-[8.75rem] whitespace-nowrap">
                                        {{ $transaction->transaction_type === 'Settlement' ? 'View Settlement Proof' : 'View Claim Proof' }}
                                    </a>
                                @elseif($transaction->blockchain_status === 'Pending')
                                    <form method="POST"
                                          action="{{ route('admin.blockchain-transactions.confirm', $transaction) }}"
                                          data-confirm
                                          data-confirm-title="Confirm blockchain proof?"
                                          data-confirm-message="This will mark the selected blockchain proof record as confirmed in EduNexUs."
                                          data-confirm-button="Confirm proof"
                                          data-confirm-tone="warning"
                                          data-loading-text="Confirming proof..."
                                          data-loader-title="Confirming blockchain proof..."
                                          data-loader-message="Updating the Morph proof record status in the EduNexUs audit console.">
                                        @csrf

                                        <button type="submit"
                                                class="inline-flex min-h-10 items-center justify-center rounded-xl border border-cyan-200 px-4 py-2 text-xs font-semibold text-cyan-700 transition hover:bg-cyan-50">
                                            Confirm
                                        </button>
                                    </form>
                                @else
                                    <span class="text-sm text-ui-subtext">
                                        No proof link
                                    </span>
                                @endif
                            </td>
                        </tr>

                        <tr class="border-t border-ui-border/40 bg-ui-surface">
                            <td colspan="6" class="px-5 pb-6">
                                <div class="proof-review-card rounded-2xl border border-ui-border bg-gradient-to-br from-white via-ui-canvas/80 to-cyan-50/40 p-4 shadow-sm shadow-slate-200/70">
                                    <div class="flex flex-col gap-2 border-b border-ui-border/70 pb-4 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <p class="text-sm font-semibold text-ui-text">Institutional Proof Review</p>
                                            <p class="mt-1 text-xs text-ui-subtext">
                                                Always-visible audit view of workflow state, proof layers, relationships, and Morph actions.
                                            </p>
                                        </div>
                                        <x-status-badge status="Expanded audit view" tone="proof" size="xs" />
                                    </div>

                                    <div class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-[1.15fr_.85fr]">
                                        <div class="rounded-2xl border border-ui-border bg-white/90 p-4 shadow-sm shadow-slate-200/60">
                                            <div class="flex flex-wrap gap-2">
                                                <x-status-badge :status="$recordedLabel($transaction)" :tone="$transaction->recorded_at ? 'success' : 'warning'" />
                                                <x-status-badge :status="$proofHash ? 'Hash Recorded on Morph' : 'Hash Pending'" :tone="$proofHash ? 'proof' : 'warning'" />
                                                <x-status-badge :status="$proofHash ? 'Timestamp Integrity Verified' : 'Timestamp Pending'" :tone="$proofHash ? 'success' : 'warning'" />
                                            </div>

                                            <div class="mt-4 grid grid-cols-1 gap-3 lg:grid-cols-3">
                                                <div class="proof-layer-card rounded-xl border p-3">
                                                    <p class="text-xs font-bold uppercase tracking-wide text-ui-action">Claim Validation Proof</p>
                                                    <p class="mt-1 text-xs leading-5 text-ui-subtext">Generated when the merchant validates the claim.</p>
                                                    <p class="mt-2 break-all font-mono text-[11px] font-semibold text-ui-text">{{ $transaction->transaction_type === 'Claim' ? ($hash ?? 'No claim proof hash') : ($proofHash ? substr($proofHash, 0, 16) . '...' . substr($proofHash, -12) : 'See claim record') }}</p>
                                                    @if($transaction->transaction_type === 'Claim' && $hasRealHash)
                                                        <a href="{{ $explorerBaseUrl . $hash }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex rounded-lg border border-ui-action/20 bg-ui-action/10 px-2.5 py-1.5 text-[11px] font-semibold text-ui-action">View Claim Proof on Morph</a>
                                                    @endif
                                                </div>

                                                <div class="proof-layer-card rounded-xl border p-3">
                                                    <p class="text-xs font-bold uppercase tracking-wide text-teal-700">Settlement Release Proof</p>
                                                    <p class="mt-1 text-xs leading-5 text-ui-subtext">Generated when the cooperative releases payout.</p>
                                                    <p class="mt-2 break-all font-mono text-[11px] font-semibold text-teal-900">{{ $transaction->transaction_type === 'Settlement' ? ($hash ?? 'No settlement proof hash') : ($linkedSettlementReference ?? 'Generated after payout release') }}</p>
                                                    @if($transaction->transaction_type === 'Settlement' && $hasRealHash)
                                                        <a href="{{ $explorerBaseUrl . $hash }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex rounded-lg border border-teal-100 bg-white px-2.5 py-1.5 text-[11px] font-semibold text-teal-700">View Settlement Proof on Morph</a>
                                                    @endif
                                                </div>

                                                <div class="proof-layer-card rounded-xl border p-3">
                                                    <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">EDUX Settlement Token Transfer</p>
                                                    <p class="mt-1 text-xs leading-5 text-ui-subtext">Real Morph testnet token transfer generated after payout release.</p>
                                                    <p class="mt-2 break-all font-mono text-[11px] font-semibold text-cyan-900">{{ $eduxLayerCopy }}</p>
                                                    @if($eduxCanOpen)
                                                        <a href="{{ $explorerBaseUrl . $eduxTransfer['edux_transaction_hash'] }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex rounded-lg border border-cyan-100 bg-white px-2.5 py-1.5 text-[11px] font-semibold text-cyan-700">View EDUX Transfer on Morph</a>
                                                    @endif
                                                </div>
                                            </div>

                                            <dl class="mt-4 grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
                                                <div class="md:col-span-2">
                                                    <dt class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Relationship Anchor</dt>
                                                    <dd class="mt-1 break-all font-mono text-xs font-semibold text-ui-text">Claim Reference: {{ $claimReference }}</dd>
                                                    <dd class="mt-1 break-all font-mono text-xs text-ui-subtext">{{ $linkedPayout?->settlement_reference ? 'Payout Reference' : 'Linked Settlement' }}: {{ $linkedSettlementReference ?? 'Generated after payout release' }}</dd>
                                                    <dd class="mt-1 break-all font-mono text-xs text-ui-subtext">Linked EDUX Transfer: {{ $transaction->transaction_type === 'Settlement' ? ($eduxHash ? substr($eduxHash, 0, 14) . '...' . substr($eduxHash, -12) : $eduxPendingCopy) : 'Generated after settlement release' }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Event Type</dt>
                                                    <dd class="mt-1 font-semibold text-ui-text">{{ str($eventType)->replace('_', ' ')->title() }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Approved Amount</dt>
                                                    <dd class="mt-1 font-semibold text-ui-text">&#8369;{{ number_format((float) $approvedAmount, 2) }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Member</dt>
                                                    <dd class="mt-1 font-semibold text-ui-text">{{ $assistanceRequest?->member?->name ?? 'Not linked' }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Merchant</dt>
                                                    <dd class="mt-1 font-semibold text-ui-text">{{ $merchantProfile?->business_name ?? $merchant?->name ?? 'Not linked' }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Settlement Status</dt>
                                                    <dd class="mt-1">
                                                        <x-status-badge :status="$settlementLabel($settlement)" :tone="$settlementTone($settlement)" size="xs" />
                                                    </dd>
                                                </div>
                                                <div>
                                                    <dt class="text-xs font-semibold uppercase tracking-wide text-ui-subtext">Recorded</dt>
                                                    <dd class="mt-1 font-semibold text-ui-text">{{ $transaction->recorded_at?->format('M d, Y g:i A') ?? 'Awaiting timestamp' }}</dd>
                                                </div>
                                            </dl>

                                            @if($settlementRail || $payoutChannel)
                                                <div class="mt-4 rounded-xl border border-teal-100 bg-teal-50 p-3">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-teal-700">Settlement Rail</p>
                                                    <dl class="mt-3 grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
                                                        <div>
                                                            <dt class="text-xs text-teal-700">Peso Payout</dt>
                                                            <dd class="font-semibold text-teal-900">&#8369;{{ number_format((float) ($payload['peso_amount'] ?? $approvedAmount), 2) }}</dd>
                                                        </div>
                                                        <div>
                                                            <dt class="text-xs text-teal-700">Merchant Payout Channel</dt>
                                                            <dd class="font-semibold text-teal-900">{{ $payoutChannel ?? 'GCash/PHP simulation' }}</dd>
                                                        </div>
                                                        <div>
                                                            <dt class="text-xs text-teal-700">Settlement Rail</dt>
                                                            <dd class="font-semibold text-teal-900">{{ $settlementRail ?? 'EDUX Settlement Token' }}</dd>
                                                        </div>
                                                        <div>
                                                            <dt class="text-xs text-teal-700">Network</dt>
                                                            <dd class="font-semibold text-teal-900">{{ $network ?? 'Morph testnet' }}</dd>
                                                        </div>
                                                        <div class="md:col-span-2">
                                                            <dt class="text-xs text-teal-700">Settlement Reference</dt>
                                                            <dd class="break-all font-mono text-xs font-semibold text-teal-900">{{ $settlementReference ?? 'Reference unavailable' }}</dd>
                                                        </div>
                                                    </dl>
                                                </div>
                                            @endif

                                            @if($transaction->transaction_type === 'Settlement')
                                                <div class="mt-4 rounded-xl border border-cyan-100 bg-cyan-50 p-3">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">EDUX Settlement Token Proof</p>
                                                        <x-status-badge :status="$eduxLabel($eduxTransfer)" :tone="$eduxTone($eduxTransfer)" size="xs" />
                                                    </div>
                                                    <p class="mt-2 text-xs leading-5 text-cyan-700">
                                                        EDUX is EduNexUs' demo settlement token used to record real settlement movement on Morph testnet while PHP/GCash payout remains operationally simulated.
                                                    </p>
                                                    <dl class="mt-3 grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
                                                        <div>
                                                            <dt class="text-xs text-cyan-700">Token Amount</dt>
                                                            <dd class="font-semibold text-cyan-900">{{ $eduxTransfer['edux_amount'] ?? '1' }} {{ $eduxTransfer['edux_token_symbol'] ?? 'EDUX' }}</dd>
                                                        </div>
                                                        <div>
                                                            <dt class="text-xs text-cyan-700">Block</dt>
                                                            <dd class="font-semibold text-cyan-900">{{ $eduxTransfer['edux_block_number'] ?? 'Not recorded' }}</dd>
                                                        </div>
                                                        <div class="md:col-span-2">
                                                            <dt class="text-xs text-cyan-700">Recipient Wallet</dt>
                                                            <dd class="break-all font-mono text-xs font-semibold text-cyan-900">{{ $eduxTransfer['edux_to'] ?? 'Not configured' }}</dd>
                                                        </div>
                                                        <div class="md:col-span-2">
                                                            <dt class="text-xs text-cyan-700">Token Contract</dt>
                                                            <dd class="break-all font-mono text-xs font-semibold text-cyan-900">{{ $eduxTransfer['edux_token_contract'] ?? 'Not configured' }}</dd>
                                                        </div>
                                                        @if($eduxTransfer['edux_transaction_hash'] ?? null)
                                                            <div class="md:col-span-2">
                                                                <dt class="text-xs text-cyan-700">EDUX Transaction Hash</dt>
                                                                <dd class="break-all font-mono text-xs font-semibold text-cyan-900">{{ $eduxTransfer['edux_transaction_hash'] }}</dd>
                                                            </div>
                                                        @endif
                                                        @if($eduxTransfer['edux_error'] ?? null)
                                                            <div class="md:col-span-2">
                                                                <dt class="text-xs text-rose-700">Transfer Note</dt>
                                                                <dd class="break-all text-xs font-semibold text-rose-800">{{ $eduxTransfer['edux_error'] }}</dd>
                                                            </div>
                                                        @endif
                                                    </dl>
                                                </div>
                                            @endif

                                            @if($proofHash)
                                                <div class="mt-4 rounded-xl border border-cyan-100 bg-cyan-50 p-3">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-700">Verified Proof Hash</p>
                                                    <p class="mt-1 break-all font-mono text-xs font-semibold text-cyan-900">{{ $proofHash }}</p>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="space-y-4">
                                            <div class="rounded-2xl border border-ui-border bg-white/90 p-4 shadow-sm shadow-slate-200/60">
                                                <p class="text-sm font-semibold text-ui-text">Linked Workflow Chain</p>
                                                <p class="mt-1 text-xs text-ui-subtext">Claim reference stays the primary audit anchor across settlement and EDUX layers.</p>
                                                <div class="mt-4 flex flex-wrap items-center gap-2 text-xs font-semibold">
                                                    @foreach($workflowSteps as $step)
                                                        <span class="rounded-full px-3 py-1.5 {{ $step['state'] === 'done' ? 'bg-emerald-50 text-ui-success ring-1 ring-emerald-100' : ($step['state'] === 'failed' ? 'bg-rose-50 text-ui-danger ring-1 ring-rose-100' : 'bg-amber-50 text-ui-warning ring-1 ring-amber-100') }}">
                                                            {{ $step['label'] }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="rounded-2xl border border-ui-border bg-white/90 p-4 shadow-sm shadow-slate-200/60">
                                                <p class="text-sm font-semibold text-ui-text">Validation Summary</p>
                                                <p class="mt-1 text-sm text-ui-subtext">
                                                    {{ $totalRules > 0 ? $passedRules . ' of ' . $totalRules . ' governance checks passed.' : 'No structured validation summary stored for this proof.' }}
                                                </p>

                                                @if(count($validationRules) > 0)
                                                    <div class="mt-3 space-y-2">
                                                        @foreach($validationRules as $rule)
                                                            <div class="flex items-start justify-between gap-3 rounded-xl border {{ ($rule['passed'] ?? false) ? 'border-emerald-100 bg-emerald-50' : 'border-amber-100 bg-amber-50' }} px-3 py-2">
                                                                <div>
                                                                    <p class="text-xs font-semibold {{ ($rule['passed'] ?? false) ? 'text-emerald-800' : 'text-amber-800' }}">{{ $rule['label'] ?? 'Governance check' }}</p>
                                                                    <p class="mt-1 text-xs {{ ($rule['passed'] ?? false) ? 'text-emerald-700' : 'text-amber-700' }}">{{ $rule['message'] ?? 'No message stored.' }}</p>
                                                                </div>
                                                                <x-status-badge :status="($rule['passed'] ?? false) ? 'Passed' : 'Review'" :tone="($rule['passed'] ?? false) ? 'success' : 'warning'" size="xs" />
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="mx-auto max-w-md">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-ui-canvas text-ui-subtext">
                                        <x-icon name="link" size="h-8 w-8" />
                                    </div>

                                    <h3 class="mt-5 text-lg font-semibold text-ui-text">
                                        No verification records found
                                    </h3>

                                    <p class="mt-2 text-sm text-ui-subtext">
                                        {{ $hasFilters
                                            ? 'No blockchain proof records match the selected filters. Clear filters to return to the full console.'
                                            : 'Records will appear after a merchant processes a valid claim and Morph proof recording runs.' }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-ui-border/80">
            @forelse($transactions as $transaction)
                @php
                    $hash = $transaction->transaction_hash;
                    $hasRealHash = filled($hash) && preg_match('/^0x[a-fA-F0-9]{64}$/', (string) $hash);
                    $shortHash = $hasRealHash
                        ? substr($hash, 0, 10) . '...' . substr($hash, -8)
                        : ($hash ?? 'Not available');
                    $payload = $payloads[$transaction->id] ?? [];
                    $assistanceRequest = $assistanceRequests[$transaction->reference_id] ?? null;
                    $settlement = $settlements[$transaction->reference_id] ?? null;
                    $merchantId = $payload['merchant_id'] ?? data_get($payload, 'proof_bundle.merchant_id');
                    $merchant = $merchantId ? ($merchants[$merchantId] ?? null) : null;
                    $merchantProfile = $merchant?->merchantProfile;
                    $proofHash = $payload['proof_hash'] ?? null;
                    $proofBundle = $payload['proof_bundle'] ?? [];
                    $validationRules = $payload['validation_rules'] ?? data_get($proofBundle, 'validation_rules', []);
                    $validationSummary = $payload['validation_summary'] ?? null;
                    $passedRules = (int) ($validationSummary['passed'] ?? collect($validationRules)->where('passed', true)->count());
                    $failedRules = (int) ($validationSummary['failed'] ?? collect($validationRules)->where('passed', false)->count());
                    $totalRules = $passedRules + $failedRules;
                    $ruleValidationPassed = (bool) ($validationSummary['all_passed'] ?? ($totalRules > 0 && $failedRules === 0));
                    $eventType = $payload['event_type'] ?? data_get($proofBundle, 'event_type', $transaction->transaction_type);
                    $approvedAmount = $payload['claim_amount'] ?? data_get($proofBundle, 'approved_amount') ?? $assistanceRequest?->approved_amount;
                    $settlementRail = $payload['settlement_rail'] ?? data_get($proofBundle, 'settlement_rail');
                    $payoutChannel = $payload['payout_channel'] ?? data_get($proofBundle, 'payout_channel');
                    $settlementReference = $payload['settlement_reference'] ?? data_get($proofBundle, 'settlement_reference');
                    $network = $payload['network'] ?? data_get($proofBundle, 'network');
                    $linkedPayout = $settlement?->payouts?->first();
                    $eduxTransfer = ($payload['edux_transfer'] ?? data_get($proofBundle, 'edux_transfer', [])) ?: ($linkedPayout?->metadata ?? []);
                    $claimReference = $transaction->reference_code ?? $assistanceRequest?->reference_code ?? 'Pending reference';
                    $linkedSettlementReference = $settlementReference ?? $linkedPayout?->settlement_reference ?? ($settlement ? 'Settlement #' . $settlement->id : null);
                    $eduxHash = $eduxTransfer['edux_transaction_hash'] ?? null;
                    $eduxStatus = $eduxTransfer['edux_transfer_status'] ?? null;
                    $settlementReleased = $settlement && in_array($settlement->status, ['Released', 'Settled'], true);
                    $settlementPendingLabel = $settlement ? 'Awaiting Settlement Release' : 'Settlement Not Generated';
                    $settlementStageLabel = $settlementReleased ? 'Settlement Released' : $settlementPendingLabel;
                    $eduxStageLabel = ! $settlementReleased
                        ? 'Waiting for Settlement Release'
                        : (($eduxStatus === 'success' || $eduxHash) ? 'EDUX Transfer Recorded' : ($eduxStatus === 'failed' ? 'EDUX Transfer Failed' : 'EDUX Transfer Skipped'));
                    $eduxPendingCopy = ! $settlementReleased
                        ? 'Waiting for settlement release'
                        : (($eduxStatus === 'failed') ? ($eduxTransfer['edux_error'] ?? 'EDUX transfer failed safely') : (($eduxStatus === 'skipped' || blank($eduxHash)) ? 'EDUX transfer disabled for this payout' : $eduxHash));
                    $workflowSteps = [
                        ['label' => 'Request Submitted', 'state' => $assistanceRequest ? 'done' : 'pending'],
                        ['label' => $assistanceRequest?->status === 'Approved' || $assistanceRequest?->approved_amount ? 'Approved' : 'Awaiting Approval', 'state' => $assistanceRequest?->status === 'Approved' || $assistanceRequest?->approved_amount ? 'done' : 'pending'],
                        ['label' => $transaction->transaction_type === 'Claim' || $assistanceRequest?->is_claimed ? 'Merchant Validated' : 'Awaiting Merchant Validation', 'state' => $transaction->transaction_type === 'Claim' || $assistanceRequest?->is_claimed ? 'done' : 'pending'],
                        ['label' => $settlement ? 'Settlement Generated' : 'Awaiting Settlement Generation', 'state' => $settlement ? 'done' : 'pending'],
                        ['label' => $settlementStageLabel, 'state' => $settlementReleased ? 'done' : 'pending'],
                        ['label' => $eduxStageLabel, 'state' => ($eduxStatus === 'success' || $eduxHash) ? 'done' : ($eduxStatus === 'failed' ? 'failed' : 'pending')],
                        ['label' => $transaction->blockchain_status === 'Confirmed' ? 'Morph Proof Confirmed' : ($transaction->blockchain_status === 'Failed' ? 'Morph Proof Failed' : 'Morph Proof Pending'), 'state' => $transaction->blockchain_status === 'Confirmed' ? 'done' : ($transaction->blockchain_status === 'Failed' ? 'failed' : 'pending')],
                    ];
                    $primaryReferenceLabel = $transaction->transaction_type === 'Settlement'
                        ? ($linkedPayout?->settlement_reference ? 'Payout Reference' : 'Settlement Reference')
                        : 'Claim Reference';
                    $primaryReference = $transaction->transaction_type === 'Settlement'
                        ? ($linkedSettlementReference ?? 'Settlement reference pending')
                        : $claimReference;
                    $eduxCanOpen = $transaction->transaction_type === 'Settlement'
                        && filled($eduxHash)
                        && str_starts_with((string) $eduxHash, '0x');
                    $eduxLayerCopy = $transaction->transaction_type === 'Claim'
                        ? 'Generated after settlement release'
                        : $eduxPendingCopy;
                @endphp

                <article class="p-4">
                    <div class="grid gap-4 rounded-2xl border border-ui-border bg-white p-4 shadow-sm shadow-slate-200/60 xl:grid-cols-[1.1fr_1.75fr_1fr] xl:items-start">
                        <div class="flex min-w-0 items-start gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-ui-canvas text-ui-action ring-1 ring-ui-border">
                                <x-icon :name="$typeIcons($transaction->transaction_type)" size="h-5 w-5" />
                            </div>

                            <div class="min-w-0">
                                <div class="flex flex-wrap gap-1.5">
                                    <x-status-badge :status="$transaction->transaction_type" :tone="$typeTone($transaction->transaction_type)" />
                                    <x-status-badge :status="$transaction->blockchain_status" :tone="$statusTone($transaction->blockchain_status)" />
                                    <x-status-badge :status="$integrityLabel($proofHash, $payload)" :tone="$proofHash ? 'success' : 'neutral'" />
                                    <x-status-badge :status="$settlementLabel($settlement)" :tone="$settlementTone($settlement)" />
                                    @if($ruleValidationPassed)
                                        <x-status-badge status="Validation Passed" tone="success" />
                                    @endif
                                    @if($settlementRail)
                                        <x-status-badge status="EDUX settlement rail" tone="proof" />
                                    @endif
                                    @if($transaction->transaction_type === 'Settlement')
                                        <x-status-badge :status="$eduxLabel($eduxTransfer)" :tone="$eduxTone($eduxTransfer)" />
                                    @endif
                                </div>

                                <div class="mt-3 rounded-xl border border-ui-border bg-gradient-to-br from-white to-ui-canvas/70 px-3 py-2 shadow-sm shadow-slate-200/50">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">{{ $primaryReferenceLabel }}</p>
                                    <p class="mt-1 break-all font-mono text-sm font-bold text-ui-text">{{ $primaryReference }}</p>
                                    <p class="mt-1 text-xs text-ui-subtext">Reference #{{ $transaction->reference_id }}</p>
                                </div>

                            <dl class="mt-3 grid grid-cols-1 gap-2 text-sm">
                                <div>
                                    <dt class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Member</dt>
                                    <dd class="mt-0.5 font-semibold text-ui-text">{{ $assistanceRequest?->member?->name ?? 'Member not linked' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Merchant</dt>
                                    <dd class="mt-0.5 font-semibold text-ui-text">{{ $merchantProfile?->business_name ?? $merchant?->name ?? 'Merchant not linked' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Program</dt>
                                    <dd class="mt-0.5 font-semibold text-ui-text">{{ $assistanceRequest?->program?->program_name ?? 'Program not linked' }}</dd>
                                </div>
                            </dl>

                        </div>
                        </div>

                    <div class="space-y-3">
                        <div class="rounded-xl border border-ui-border bg-white/90 p-3 shadow-sm shadow-slate-200/60">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Workflow</p>
                            <div class="mt-2 flex flex-wrap gap-1.5 text-[11px] font-semibold">
                                @foreach($workflowSteps as $step)
                                    <span class="rounded-full px-2.5 py-1 {{ $step['state'] === 'done' ? 'bg-emerald-50 text-ui-success ring-1 ring-emerald-100' : ($step['state'] === 'failed' ? 'bg-rose-50 text-ui-danger ring-1 ring-rose-100' : 'bg-amber-50 text-ui-warning ring-1 ring-amber-100') }}">{{ $step['label'] }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <div class="rounded-xl border border-ui-border bg-white/90 p-3 shadow-sm shadow-slate-200/60">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Traceability</p>
                                <dl class="mt-2 space-y-1 text-xs">
                                    <div>
                                        <dt class="font-semibold text-ui-subtext">Linked Claim</dt>
                                        <dd class="break-all font-mono font-semibold text-ui-text">{{ $claimReference }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-semibold text-ui-subtext">Linked Settlement</dt>
                                        <dd class="break-all font-mono text-ui-text">{{ $linkedSettlementReference ?? 'Generated after payout release' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="font-semibold text-ui-subtext">Linked EDUX Transfer</dt>
                                        <dd class="break-all font-mono text-ui-text">{{ $transaction->transaction_type === 'Settlement' ? ($eduxHash ? substr($eduxHash, 0, 12) . '...' . substr($eduxHash, -10) : $eduxPendingCopy) : 'Generated after settlement release' }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="rounded-xl border border-ui-border bg-white/90 p-3 shadow-sm shadow-slate-200/60">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-ui-subtext">Validation</p>
                                <p class="mt-2 text-sm font-bold text-ui-text">{{ $totalRules > 0 ? $passedRules . ' passed checks' : 'No checks stored' }}</p>
                                <p class="mt-1 text-xs font-semibold {{ $failedRules > 0 ? 'text-ui-danger' : 'text-ui-success' }}">{{ $failedRules > 0 ? $failedRules . ' failed checks' : 'No failed checks' }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-3 2xl:grid-cols-3">
                        <div class="proof-layer-card rounded-xl border p-3 min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wide text-ui-action">Claim Validation Proof</p>
                            <p class="mt-2 break-all font-mono text-[11px] font-semibold text-ui-text">{{ $transaction->transaction_type === 'Claim' ? ($hash ?? 'No claim proof hash') : ($proofHash ? substr($proofHash, 0, 16) . '...' . substr($proofHash, -12) : 'See claim record') }}</p>
                            @if($transaction->transaction_type === 'Claim' && $hasRealHash)
                                <a href="{{ $explorerBaseUrl . $hash }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex rounded-lg border border-ui-action/20 bg-ui-action/10 px-2.5 py-1.5 text-[11px] font-semibold text-ui-action">View Claim Proof</a>
                            @endif
                        </div>

                        <div class="proof-layer-card rounded-xl border p-3 min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wide text-teal-700">Settlement Release Proof</p>
                            <p class="mt-2 break-all font-mono text-[11px] font-semibold text-teal-900">{{ $transaction->transaction_type === 'Settlement' ? ($hash ?? 'No settlement proof hash') : ($linkedSettlementReference ?? 'Generated after payout release') }}</p>
                            @if($transaction->transaction_type === 'Settlement' && $hasRealHash)
                                <a href="{{ $explorerBaseUrl . $hash }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex rounded-lg border border-teal-100 bg-white px-2.5 py-1.5 text-[11px] font-semibold text-teal-700">View Settlement Proof</a>
                            @endif
                        </div>

                        <div class="proof-layer-card rounded-xl border p-3 min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wide text-cyan-700">EDUX Settlement Token Transfer</p>
                            <p class="mt-2 break-all font-mono text-[11px] font-semibold text-cyan-900">{{ $eduxLayerCopy }}</p>
                            @if($eduxCanOpen)
                                <a href="{{ $explorerBaseUrl . $eduxTransfer['edux_transaction_hash'] }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex rounded-lg border border-cyan-100 bg-white px-2.5 py-1.5 text-[11px] font-semibold text-cyan-700">View EDUX Transfer</a>
                            @endif
                        </div>
                    </div>

                    </div>

                    <div class="space-y-3">
                    <dl class="space-y-3 text-sm">
                        <div class="rounded-xl border border-ui-border bg-ui-canvas/70 p-3">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext">Transaction Hash</dt>
                            <dd class="mt-1 break-all font-mono text-xs font-semibold text-ui-text" title="{{ $hash ?? 'No transaction hash recorded' }}">
                                {{ $shortHash }}
                            </dd>
                            <dd class="mt-1 text-xs text-ui-subtext">
                                {{ $hasRealHash ? 'Morph transaction hash' : 'No explorer hash available' }}
                            </dd>
                        </div>

                        @if($proofHash)
                            <div class="rounded-xl border border-cyan-100 bg-cyan-50 p-3">
                                <dt class="text-xs font-medium uppercase tracking-wide text-cyan-700">Proof Bundle Hash</dt>
                                <dd class="mt-1 break-all font-mono text-xs font-semibold text-cyan-800" title="{{ $proofHash }}">
                                    {{ substr($proofHash, 0, 18) }}...{{ substr($proofHash, -12) }}
                                </dd>
                            </div>
                        @endif

                        <div class="rounded-xl border border-ui-border bg-ui-canvas/70 p-3">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext">Recorded</dt>
                            <dd class="mt-1 font-semibold text-ui-text">
                                {{ $transaction->recorded_at?->format('M d, Y') ?? 'Not recorded' }}
                            </dd>
                            <dd class="mt-1 text-xs text-ui-subtext">
                                {{ $transaction->recorded_at?->format('g:i A') ?? 'Awaiting timestamp' }}
                            </dd>
                        </div>

                        <div class="hidden">
                            <dt class="text-xs font-medium uppercase tracking-wide text-ui-subtext">Proof Layer</dt>
                            <dd class="mt-1 font-semibold text-ui-text">
                                {{ str($eventType)->replace('_', ' ')->title() }}
                            </dd>
                            <dd class="mt-1 text-xs text-ui-subtext">
                                {{ $totalRules > 0 ? $passedRules . ' of ' . $totalRules . ' governance checks passed' : 'Console verification record' }}
                            </dd>
                        </div>

                        @if($settlementRail || $payoutChannel)
                            <div class="hidden">
                                <dt class="text-xs font-medium uppercase tracking-wide text-teal-700">Settlement Rail</dt>
                                <dd class="mt-1 font-semibold text-teal-900">&#8369;{{ number_format((float) ($payload['peso_amount'] ?? $approvedAmount), 2) }} via {{ $payoutChannel ?? 'GCash/PHP simulation' }}</dd>
                                <dd class="mt-1 text-xs text-teal-700">{{ $settlementRail ?? 'EDUX Settlement Token' }} · {{ $network ?? 'Morph testnet' }}</dd>
                                <dd class="mt-1 break-all font-mono text-xs font-semibold text-teal-900">{{ $settlementReference ?? 'Reference unavailable' }}</dd>
                            </div>
                        @endif

                        @if($transaction->transaction_type === 'Settlement')
                            <div class="hidden">
                                <dt class="text-xs font-medium uppercase tracking-wide text-cyan-700">EDUX Settlement Token Proof</dt>
                                <dd class="mt-1 font-semibold text-cyan-900">{{ $eduxLabel($eduxTransfer) }}</dd>
                                <dd class="mt-1 text-xs text-cyan-700">EDUX records real settlement movement on Morph testnet while PHP/GCash payout remains operationally simulated.</dd>
                                <dd class="mt-1 text-xs text-cyan-700">{{ $eduxTransfer['edux_amount'] ?? '1' }} {{ $eduxTransfer['edux_token_symbol'] ?? 'EDUX' }} to Morph testnet recipient</dd>
                                <dd class="mt-1 break-all font-mono text-xs font-semibold text-cyan-900">{{ $eduxTransfer['edux_transaction_hash'] ?? ($eduxTransfer['edux_error'] ?? 'No EDUX transfer hash recorded') }}</dd>
                                @if($eduxTransfer['edux_to'] ?? null)
                                    <dd class="mt-1 break-all font-mono text-[11px] text-cyan-800">Recipient: {{ $eduxTransfer['edux_to'] }}</dd>
                                @endif
                            </div>
                        @endif
                    </dl>

                    <div class="mt-3 flex justify-center sm:justify-start xl:justify-end">
                        @if($hasRealHash)
                            <a href="{{ $explorerBaseUrl . $hash }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="btn-proof w-full justify-center whitespace-nowrap sm:w-auto sm:min-w-[8.75rem]">
                                {{ $transaction->transaction_type === 'Settlement' ? 'View Settlement Proof' : 'View Claim Proof' }}
                            </a>
                        @elseif($transaction->blockchain_status === 'Pending')
                            <form method="POST"
                                  action="{{ route('admin.blockchain-transactions.confirm', $transaction) }}"
                                  data-confirm
                                  data-confirm-title="Confirm blockchain proof?"
                                  data-confirm-message="This will mark the selected blockchain proof record as confirmed in EduNexUs."
                                  data-confirm-button="Confirm proof"
                                  data-confirm-tone="warning"
                                  data-loading-text="Confirming proof..."
                                  data-loader-title="Confirming blockchain proof..."
                                  data-loader-message="Updating the Morph proof record status in the EduNexUs audit console.">
                                @csrf

                                <button type="submit"
                                        class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-cyan-200 px-4 py-2.5 text-sm font-semibold text-cyan-700 transition hover:bg-cyan-50 sm:w-auto">
                                    Confirm
                                </button>
                            </form>
                        @else
                            <span class="inline-flex min-h-10 items-center rounded-xl bg-ui-canvas px-4 py-2 text-sm font-semibold text-ui-subtext">
                                No proof link
                            </span>
                        @endif
                    </div>
                    </div>
                    </div>
                </article>
            @empty
                <div class="px-4 py-14 text-center">
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-ui-canvas text-ui-subtext">
                        <x-icon name="link" size="h-8 w-8" />
                    </div>

                    <h3 class="mt-5 text-lg font-semibold text-ui-text">
                        No verification records found
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm text-ui-subtext">
                        {{ $hasFilters
                            ? 'No blockchain proof records match the selected filters. Clear filters to return to the full console.'
                            : 'Records will appear after a merchant processes a valid claim and Morph proof recording runs.' }}
                    </p>
                </div>
            @endforelse
        </div>

        <x-slot:footer>
            <div class="flex flex-col items-center justify-center gap-3 text-center">
                <p class="text-sm text-ui-subtext">
                    Page {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}
                </p>

                <div class="flex max-w-full justify-center overflow-x-auto">
                    {{ $transactions->links() }}
                </div>
            </div>
        </x-slot:footer>
    </x-table-card>
</div>

@endsection
