<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-secondary shadow-sm transition hover:bg-ui-canvas focus:outline-none focus:ring-4 focus:ring-ui-action/15 disabled:opacity-60']) }}>
    {{ $slot }}
</button>
