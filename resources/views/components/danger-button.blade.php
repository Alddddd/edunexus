<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-danger border border-transparent transition hover:bg-rose-700 focus:outline-none focus:ring-4 focus:ring-ui-danger/20']) }}>
    {{ $slot }}
</button>
