@props([
    'title' => null,
    'description' => null,
])

<section {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl border border-ui-border/90 bg-ui-surface shadow-[0_14px_32px_rgba(15,47,44,0.06)] ring-1 ring-white/60']) }}>
    @if($title || $description || isset($actions))
        <div class="flex flex-col gap-4 border-b border-ui-border/80 px-4 py-4 sm:px-6 sm:py-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                @if($title)
                    <h2 class="text-base font-semibold text-ui-text">
                        {{ $title }}
                    </h2>
                @endif

                @if($description)
                    <p class="mt-1 text-sm leading-6 text-ui-subtext">
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
    @endif

    <div class="overflow-x-auto">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="border-t border-ui-border/80 bg-ui-canvas/60 px-4 py-4 sm:px-6">
            {{ $footer }}
        </div>
    @endisset
</section>
