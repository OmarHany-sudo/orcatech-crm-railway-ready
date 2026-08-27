<x-guest-layout>
    <x-authentication-card>
    <x-slot name="logo">
        <x-authentication-card-logo />
    </x-slot>

    <x-validation-errors class="mb-4" />

    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('orcatech.login.heading') }}
    </div>

    <div class="mb-5 rounded-xl border border-sky-200 bg-sky-50 p-4 text-sm dark:border-sky-800/60 dark:bg-sky-500/10">
        <p class="font-semibold text-sky-700 dark:text-sky-300">{{ __('orcatech.login.credentials_title') }}</p>

        <dl class="mt-2 grid grid-cols-[auto_1fr] gap-x-3 gap-y-1 text-gray-700 dark:text-gray-300">
            <dt class="text-gray-500 dark:text-gray-400">{{ __('orcatech.login.email_label') }}</dt>
            <dd><code class="rounded bg-white px-1.5 py-0.5 text-xs dark:bg-gray-900">admin@nileproperties.demo</code></dd>

            <dt class="text-gray-500 dark:text-gray-400">{{ __('orcatech.login.password_label') }}</dt>
            <dd><code class="rounded bg-white px-1.5 py-0.5 text-xs dark:bg-gray-900">orcatech-demo</code></dd>
        </dl>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-label for="email" value="{{ __('Email') }}" />
            <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
        </div>

        <div class="mt-4">
            <x-label for="password" value="{{ __('Password') }}" />
            <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="flex items-center">
                <x-checkbox id="remember_me" name="remember" />
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                   href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-button class="ms-4 bg-sky-600 hover:bg-sky-500 active:bg-sky-700 focus:border-sky-700 ring-sky-300">
                {{ __('Log in') }}
            </x-button>
        </div>
    </form>

    @if (JoelButcher\Socialstream\Socialstream::show())
        <x-socialstream />
    @endif
    </x-authentication-card>
</x-guest-layout>
