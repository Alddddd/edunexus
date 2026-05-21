@props([
    'status' => null,
    'label' => null,
    'tone' => null,
    'size' => 'sm',
])

@php
    $key = strtolower(trim((string) ($tone ?: $status ?: 'neutral')));

    $styles = [
        'approved' => 'bg-emerald-50 text-ui-success ring-emerald-200',
        'success' => 'bg-emerald-50 text-ui-success ring-emerald-200',
        'active' => 'bg-emerald-50 text-ui-success ring-emerald-200',
        'confirmed' => 'bg-emerald-50 text-ui-success ring-emerald-200',
        'verified' => 'bg-emerald-50 text-ui-success ring-emerald-200',
        'settled' => 'bg-emerald-50 text-ui-success ring-emerald-200',
        'released' => 'bg-emerald-50 text-ui-success ring-emerald-200',
        'completed' => 'bg-emerald-50 text-ui-success ring-emerald-200',
        'passed' => 'bg-emerald-50 text-ui-success ring-emerald-200',
        'ready to process' => 'bg-emerald-50 text-ui-success ring-emerald-200',
        'ready for morph proof' => 'bg-emerald-50 text-ui-success ring-emerald-200',
        'valid claim pass' => 'bg-emerald-50 text-ui-success ring-emerald-200',
        'validation passed' => 'bg-emerald-50 text-ui-success ring-emerald-200',
        'ready for release' => 'bg-amber-50 text-ui-warning ring-amber-200',
        'ready for validation' => 'bg-amber-50 text-ui-warning ring-amber-200',
        'release pending' => 'bg-amber-50 text-ui-warning ring-amber-200',
        'pending settlement' => 'bg-amber-50 text-ui-warning ring-amber-200',
        'pending verification' => 'bg-amber-50 text-ui-warning ring-amber-200',
        'pending review' => 'bg-amber-50 text-ui-warning ring-amber-200',
        'pending' => 'bg-amber-50 text-ui-warning ring-amber-200',
        'warning' => 'bg-amber-50 text-ui-warning ring-amber-200',
        'review' => 'bg-amber-50 text-ui-warning ring-amber-200',
        'validation review' => 'bg-amber-50 text-ui-warning ring-amber-200',
        'rejected' => 'bg-rose-50 text-ui-danger ring-rose-200',
        'danger' => 'bg-rose-50 text-ui-danger ring-rose-200',
        'failed' => 'bg-rose-50 text-ui-danger ring-rose-200',
        'inactive' => 'bg-rose-50 text-ui-danger ring-rose-200',
        'processing blocked' => 'bg-rose-50 text-ui-danger ring-rose-200',
        'blocked' => 'bg-rose-50 text-ui-danger ring-rose-200',
        'claimed' => 'bg-cyan-50 text-ui-proof ring-cyan-200',
        'processed' => 'bg-cyan-50 text-ui-proof ring-cyan-200',
        'proof' => 'bg-cyan-50 text-ui-proof ring-cyan-200',
        'partially released' => 'bg-cyan-50 text-ui-proof ring-cyan-200',
        'morph integrated' => 'bg-cyan-50 text-ui-proof ring-cyan-200',
        'audit-ready' => 'bg-cyan-50 text-ui-proof ring-cyan-200',
        'read' => 'bg-slate-100 text-slate-600 ring-slate-200',
        'unread' => 'bg-amber-50 text-ui-warning ring-amber-200',
        'unclaimed' => 'bg-slate-100 text-slate-600 ring-slate-200',
        'expired' => 'bg-slate-100 text-slate-600 ring-slate-200',
        'neutral' => 'bg-slate-100 text-slate-600 ring-slate-200',
    ];

    $sizes = [
        'xs' => 'px-2.5 py-1 text-xs',
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-3.5 py-2 text-sm',
    ];

    $badgeClass = $styles[$key] ?? $styles['neutral'];
    $sizeClass = $sizes[$size] ?? $sizes['sm'];
    $displayLabel = $label ?: $status;
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex w-fit max-w-full shrink-0 items-center justify-center whitespace-normal text-center leading-tight rounded-full font-semibold ring-1 ring-inset shadow-sm shadow-white/50 ' . $sizeClass . ' ' . $badgeClass]) }}>
    {{ $displayLabel }}
</span>
