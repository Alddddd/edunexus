@props([
    'title',
    'description' => null,
    'eyebrow' => null,
])

<section {{ $attributes->merge([
    'class' => 'dashboard-page-header rounded-2xl border border-ui-action/15 bg-gradient-to-br from-white via-emerald-50/70 to-teal-50/80 px-5 py-4 shadow-[0_24px_52px_rgba(6,78,59,0.12)] ring-1 ring-white/80'
]) }}>
    <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
        <div class="min-w-0">
            @if($eyebrow)
                <p class="text-sm font-semibold uppercase tracking-wider text-ui-action">
                    {{ $eyebrow }}
                </p>
            @endif

            <h1 class="mt-2 text-3xl font-bold tracking-tight text-ui-anchor">
                {{ $title }}
            </h1>

            @if($description)
                <p class="mt-2 max-w-3xl leading-6 text-ui-subtext/90">
                    {{ $description }}
                </p>
            @endif
        </div>

        @isset($actions)
            <div class="flex flex-wrap items-center gap-3 lg:justify-end">
                {{ $actions }}
            </div>
        @endisset
    </div>
</section>
