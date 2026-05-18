@props([
    'title',
    'description' => null,
    'eyebrow' => null,
])

<section {{ $attributes->merge(['class' => 'mb-6 sm:mb-8']) }}>
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div class="min-w-0">
            @if($eyebrow)
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-ui-action">
                    {{ $eyebrow }}
                </p>
            @endif

            <h1 class="mt-1 text-2xl font-bold tracking-tight text-ui-text sm:text-3xl">
                {{ $title }}
            </h1>

            @if($description)
                <p class="mt-2 max-w-3xl text-sm leading-6 text-ui-subtext sm:text-base">
                    {{ $description }}
                </p>
            @endif
        </div>

        @isset($actions)
            <div class="flex shrink-0 flex-wrap items-center gap-2">
                {{ $actions }}
            </div>
        @endisset
    </div>
</section>
