<x-filament-panels::page>
    <div class="orcatech-addons mx-auto max-w-5xl">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('orcatech.addons_page.title') }}</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('orcatech.addons_page.subtitle') }}</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->addons() as $addon)
                <div class="flex flex-col rounded-2xl border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-gray-700/60 dark:bg-gray-900">
                    <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                        <x-filament::icon :icon="$addon['icon']" class="h-6 w-6" />
                    </div>

                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $addon['name'] }}
                    </h3>

                    <p class="mt-1.5 flex-1 text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                        {{ $addon['description'] }}
                    </p>

                    <span class="mt-4 inline-flex w-fit items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        <x-filament::icon icon="heroicon-o-plus-circle" class="h-3.5 w-3.5" />
                        {{ __('orcatech.addons_page.available_as_addon') }}
                    </span>
                </div>
            @endforeach
        </div>

        <div class="mt-8 rounded-2xl border border-indigo-200 bg-gradient-to-br from-indigo-50 to-white p-6 dark:border-indigo-900/40 dark:from-indigo-950/30 dark:to-gray-900">
            <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                {{ __('orcatech.addons_page.enterprise_note') }}
            </p>

            <p class="mt-3 text-xs text-gray-400 dark:text-gray-500">
                {{ __('orcatech.enterprise_note') }}
            </p>
        </div>
    </div>
</x-filament-panels::page>
