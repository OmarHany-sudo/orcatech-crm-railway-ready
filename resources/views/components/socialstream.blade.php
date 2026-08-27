<div class="mt-6 space-y-5">
    @if(! empty(\JoelButcher\Socialstream\Socialstream::providers()))
        <div class="relative flex items-center gap-3 text-xs text-gray-400">
            <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
            <span class="shrink-0">{{ config('socialstream.prompt', 'Or Login Via') }}</span>
            <div class="h-px flex-1 bg-gray-200 dark:bg-gray-700"></div>
        </div>
    @endif

    <x-input-error :for="'socialstream'" class="text-center" />

    <div class="grid gap-3">
        @foreach (\JoelButcher\Socialstream\Socialstream::providers() as $provider)
            <a class="flex w-full items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:-translate-y-0.5 hover:border-orca-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:border-orca-700"
               href="{{ route('oauth.redirect', $provider['id']) }}">
                <x-socialstream-icons.provider-icon :provider="$provider['id']" class="h-5 w-5" />
                <span>{{ $provider['buttonLabel'] }}</span>
            </a>
        @endforeach
    </div>
</div>
