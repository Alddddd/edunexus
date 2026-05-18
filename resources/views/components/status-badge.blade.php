@props([
    'status' => null,
    'label' => null,
    'tone' => null,
    'size' => 'sm',
])

@php
    $key = strtolower(trim((string) ($tone ?: $status ?: 'neutral')));

    $styles = [
        'approved' => 'bg-emerald-50 text-ui-success ring-emerald-100',
        'success' => 'bg-emerald-50 text-ui-success ring-emerald-100',
        'active' => 'bg-emerald-50 text-ui-success ring-emerald-100',
        'settled' => 'bg-emerald-50 text-ui-success ring-emerald-100',
        'pending' => 'bg-amber-50 text-ui-warning ring-amber-100',
        'warning' => 'bg-amber-50 text-ui-warning ring-amber-100',
        'rejected' => 'bg-rose-50 text-ui-danger ring-rose-100',
        'danger' => 'bg-rose-50 text-ui-danger ring-rose-100',
        'failed' => 'bg-rose-50 text-ui-danger ring-rose-100',
        'claimed' => 'bg-cyan-50 text-ui-proof ring-cyan-100',
        'proof' => 'bg-cyan-50 text-ui-proof ring-cyan-100',
        'confirmed' => 'bg-cyan-50 text-ui-proof ring-cyan-100',
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

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full font-semibold ring-1 ring-inset ' . $sizeClass . ' ' . $badgeClass]) }}>
    {{ $displayLabel }}
</span>
