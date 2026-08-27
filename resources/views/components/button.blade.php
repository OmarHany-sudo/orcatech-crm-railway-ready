<button {{ $attributes->merge(['type' => 'submit', 'class' => 'cs-btn cs-btn--primary disabled:opacity-50']) }}>
    {{ $slot }}
</button>
