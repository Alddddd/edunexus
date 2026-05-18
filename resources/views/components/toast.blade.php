@props([
    'type' => 'info',
    'message' => '',
])

@php
    $styles = [
        'success' => [
            'accent' => 'bg-emerald-500',
            'icon' => 'check-circle',
            'iconClass' => 'bg-emerald-50 text-emerald-700 ring-emerald-100',
            'title' => 'Success',
        ],
        'error' => [
            'accent' => 'bg-rose-500',
            'icon' => 'x-circle',
            'iconClass' => 'bg-rose-50 text-rose-700 ring-rose-100',
            'title' => 'Action needed',
        ],
        'warning' => [
            'accent' => 'bg-amber-500',
            'icon' => 'alert-triangle',
            'iconClass' => 'bg-amber-50 text-amber-700 ring-amber-100',
            'title' => 'Notice',
        ],
        'info' => [
            'accent' => 'bg-teal-500',
            'icon' => 'info',
            'iconClass' => 'bg-teal-50 text-teal-700 ring-teal-100',
            'title' => 'Update',
        ],
    ];

    $toast = $styles[$type] ?? $styles['info'];
@endphp

<div data-toast
     class="group pointer-events-auto relative flex w-full max-w-md translate-y-2 items-start gap-3 overflow-hidden rounded-2xl border border-slate-200 bg-white/95 p-4 pr-11 text-slate-800 opacity-0 shadow-lg shadow-slate-200/70 backdrop-blur transition duration-300 ease-out">
    <span class="absolute inset-y-0 left-0 w-1 {{ $toast['accent'] }}"></span>

    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl ring-1 {{ $toast['iconClass'] }}">
        <x-icon :name="$toast['icon']" size="h-5 w-5" />
    </div>

    <div class="min-w-0 flex-1">
        <p class="text-sm font-semibold text-slate-900">
            {{ $toast['title'] }}
        </p>

        <p class="mt-1 text-sm leading-relaxed text-slate-600">
            {{ $message }}
        </p>
    </div>

    <button type="button"
            data-toast-close
            class="absolute right-3 top-3 flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
            aria-label="Close notification">
        <x-icon name="x" size="h-4 w-4" />
    </button>
</div>
