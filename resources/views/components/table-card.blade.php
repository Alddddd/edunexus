@props([
    'title' => null,
    'description' => null,
])

<section {{ $attributes->merge(['class' => 'w-full min-w-0 max-w-full overflow-hidden rounded-2xl border border-ui-border/80 bg-ui-surface shadow-[0_10px_30px_rgba(6,78,59,0.07)] ring-1 ring-white/70']) }}>
    @if($title || $description || isset($actions))
        <div class="flex flex-col gap-3 border-b border-ui-border/70 bg-gradient-to-r from-[#f8fdfb] to-ui-muted/30 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex min-w-0 items-start gap-3">
                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-ui-action/10 text-ui-action ring-1 ring-ui-action/15">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </div>

                <div class="min-w-0">
                @if($title)
                    <h2 class="text-sm font-bold text-ui-anchor">
                        {{ $title }}
                    </h2>
                @endif

                @if($description)
                    <p class="mt-0.5 text-xs leading-5 text-ui-subtext/70">
                        {{ $description }}
                    </p>
                @endif
                </div>
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
        <div class="border-t border-ui-border/60 bg-ui-canvas/50 px-5 pb-6 pt-5">
            {{ $footer }}
        </div>
    @endisset
</section>
