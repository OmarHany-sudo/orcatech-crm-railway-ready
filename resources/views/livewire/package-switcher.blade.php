<div class="orcatech-package-switcher">
    <div
        x-data="{ open: false }"
        class="relative"
    >
        <button
            type="button"
            class="inline-flex items-center gap-2 rounded-xl border px-3 py-1.5 text-xs font-semibold transition
                {{ $currentPackage === 'business'
                    ? 'border-sky-300 bg-sky-50 text-sky-700 hover:bg-sky-100 dark:border-sky-700 dark:bg-sky-500/10 dark:text-sky-300 dark:hover:bg-sky-500/20'
                    : 'border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 dark:border-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300 dark:hover:bg-emerald-500/20' }}"
            x-on:click="open = ! open"
            aria-haspopup="true"
            :aria-expanded="open"
        >
            <span class="relative flex h-2 w-2">
                <span class="{{ $currentPackage === 'business' ? 'bg-sky-500' : 'bg-emerald-500' }} h-2 w-2 rounded-full"></span>
            </span>
            <span class="uppercase tracking-wide opacity-60">{{ __('orcatech.switcher.demo') }}</span>
            <span>{{ __('orcatech.packages.'.$currentPackage.'.name') }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3.5 w-3.5 opacity-50">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
            </svg>
        </button>

        <div
            x-show="open"
            x-on:click.outside="open = false"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute z-50 mt-2 w-64 rounded-2xl border border-gray-200 bg-white p-2 shadow-xl end-0 dark:border-gray-700 dark:bg-gray-900"
            style="display: none"
        >
            <p class="px-3 pb-2 pt-1.5 text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                {{ __('orcatech.switcher.title') }}
            </p>

            @foreach ($packages as $key => $package)
                <button
                    type="button"
                    wire:click="switchPackage('{{ $key }}')"
                    @if ($key === $currentPackage) x-on:click="open = false" @endif
                    class="flex w-full items-center justify-between gap-3 rounded-xl px-3 py-2.5 text-start transition hover:bg-gray-50 dark:hover:bg-gray-800 {{
                        $key === $currentPackage ? ($key === 'business' ? 'bg-sky-50 dark:bg-sky-500/10' : 'bg-emerald-50 dark:bg-emerald-500/10') : ''
                    }}"
                >
                    <span>
                        <span class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white">
                            <span class="h-2 w-2 rounded-full {{ $key === 'business' ? 'bg-sky-500' : 'bg-emerald-500' }}"></span>
                            {{ __('orcatech.packages.'.$key.'.name') }}
                        </span>
                        <span class="mt-0.5 block text-xs text-gray-400 dark:text-gray-500">
                            {{ __("orcatech.packages.{$key}.tagline") }}
                        </span>
                    </span>

                    @if ($key === $currentPackage)
                        <x-filament::icon icon="heroicon-m-check-circle" class="h-5 w-5 shrink-0 {{ $key === 'business' ? 'text-sky-500' : 'text-emerald-500' }}" />
                    @else
                        <span class="shrink-0 rounded-lg bg-gray-100 px-2 py-1 text-[11px] font-bold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                            {{ number_format((float) $package['price']) }} {{ __('orcatech.currency') }}
                        </span>
                    @endif
                </button>
            @endforeach

            <p class="border-t border-gray-100 px-3 pb-1.5 pt-2 text-[11px] leading-snug text-gray-400 dark:border-gray-800 dark:text-gray-500">
                {{ __('orcatech.switcher.hint') }}
            </p>
        </div>
    </div>
</div>
