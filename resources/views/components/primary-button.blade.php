<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary border border-transparent transition hover:bg-primary-dark focus:outline-none focus:ring-4 focus:ring-ui-action/20']) }}>
    {{ $slot }}
</button>
