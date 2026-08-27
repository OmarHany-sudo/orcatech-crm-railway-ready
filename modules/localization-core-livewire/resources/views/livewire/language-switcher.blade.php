<div class="orcatech-language-switcher relative" x-data="{ open: false }">
    <button
        type="button"
        x-on:click="open = ! open"
        aria-label="{{ __('orcatech.language.change') }}"
        :aria-expanded="open"
        class="inline-flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-semibold transition"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 opacity-60" aria-hidden="true">
            <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM3.7 7.5h2.6a13 13 0 01.5-3 8 8 0 00-3.1 3zm-.2 2.5c0 .5 0 1 .1 1.5h2.8a15 15 0 010-3H3.6a12 12 0 00-.1 1.5zm.2 2.5h3.1c.1-1 .3-2 .5-3h3.4c.2 1 .4 2 .5 3h3.1c.1-.5.1-1 .1-1.5s0-1-.1-1.5h-2.9a15 15 0 00-.4-3h-3.4c-.2 1-.3 2-.4 3H3.7c-.1.5-.1 1-.1 1.5s0 1 .1 1.5zm.5 2.5h2.6a13 13 0 01-.5-3H3.7a8 8 0 002.5 3zm3.4 0h4.8a11 11 0 00-.5-3H8.3a11 11 0 00-.5 3zm5.4 0h2.6a8 8 0 003.1-3h-3.2a13 13 0 01-.5 3zm3.4-5h-2.8a15 15 0 000 3h2.8c.1-.5.1-1 .1-1.5s0-1-.1-1.5zm-3-2h2.9A8 8 0 0013 4.5c.3 1 .5 2 .5 3z" />
        </svg>
        <span>{{ strtoupper($currentLocale) }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 opacity-50" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
        </svg>
    </button>

    <div
        x-show="open"
        x-on:click.outside="open = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="scale-95 opacity-0"
        x-transition:enter-end="scale-100 opacity-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="scale-100 opacity-100"
        x-transition:leave-end="scale-95 opacity-0"
        class="absolute z-50 mt-2 w-44 origin-top-end overflow-hidden rounded-xl border bg-white p-1.5 shadow-xl end-0 dark:bg-gray-900"
        style="display: none"
    >
        @foreach ($availableLocales as $locale => $name)
            <button
                type="button"
                wire:click="switchLanguage('{{ $locale }}')"
                x-on:click="open = false"
                class="flex w-full items-center justify-between gap-2 rounded-lg px-3 py-2 text-start text-sm transition hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-800 {{ $locale === $currentLocale ? 'font-semibold text-primary-600 dark:text-primary-400' : '' }}"
            >
                <span>{{ $name }}</span>
                @if ($locale === $currentLocale)
                    <x-filament::icon icon="heroicon-m-check" class="h-4 w-4 text-primary-500" />
                @endif
            </button>
        @endforeach
    </div>
</div>
