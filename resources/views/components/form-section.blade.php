@props([
    'title' => null,
    'description' => null,
    'columns' => '1',
])

@php
    $gridClass = match ((string) $columns) {
        '2' => 'grid grid-cols-1 gap-5 md:grid-cols-2',
        '3' => 'grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3',
        default => 'space-y-5',
    };
@endphp

<section {{ $attributes->merge(['class' => 'space-y-4']) }}>
    @if($title || $description)
        <div>
            @if($title)
                <h3 class="text-sm font-semibold text-ui-text">
                    {{ $title }}
                </h3>
            @endif

            @if($description)
                <p class="mt-1 text-sm leading-6 text-ui-subtext">
                    {{ $description }}
                </p>
            @endif
        </div>
    @endif

    <div class="{{ $gridClass }}">
        {{ $slot }}
    </div>
</section>
