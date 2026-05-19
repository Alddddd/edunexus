@props([
    'title' => null,
    'description' => null,
])

<section {{ $attributes->merge(['class' => 'w-full min-w-0 max-w-full overflow-hidden rounded-2xl border border-ui-border/95 bg-ui-surface shadow-[0_18px_40px_rgba(6,78,59,0.08)] ring-1 ring-white/80']) }}>
    @if($title || $description || isset($actions))
        <div class="flex flex-col gap-4 border-b border-ui-border/85 bg-gradient-to-r from-ui-surface to-ui-muted/45 px-4 py-5 sm:px-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="min-w-0">
                @if($title)
                    <h2 class="text-base font-bold text-ui-anchor">
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

    <div class="max-w-full overflow-x-auto">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="border-t border-ui-border/80 bg-ui-canvas/60 px-4 py-4 sm:px-6">
            {{ $footer }}
        </div>
    @endisset
</section>
