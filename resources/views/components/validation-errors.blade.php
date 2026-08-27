@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'cs-errors']) }} role="alert" aria-live="polite">
        <div class="cs-errors__title">{{ __('Whoops! Something went wrong.') }}</div>

        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
