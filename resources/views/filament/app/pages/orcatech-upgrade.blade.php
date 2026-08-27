<x-filament-panels::page>
    @php
        $business = $this->businessPackage();
        $businessFeatures = \App\Support\OrcaTech\Feature::businessFeatures();
    @endphp

    <div class="orcatech-upgrade mx-auto w-full max-w-4xl">
        <div class="overflow-hidden rounded-[1.5rem] border border-orca-200 bg-white shadow-[0_24px_70px_rgba(18,33,40,0.12)] dark:border-orca-900/50 dark:bg-gray-950">
            <div class="relative overflow-hidden bg-[linear-gradient(120deg,#0f3c3a_0%,#0d6e61_55%,#176d82_100%)] px-6 py-10 text-white sm:px-10">
                <div class="pointer-events-none absolute -end-16 -top-24 h-64 w-64 rounded-full border-[28px] border-white/10"></div>
                <div class="pointer-events-none absolute -bottom-40 start-1/3 h-72 w-72 rounded-full border-[36px] border-white/5"></div>

                <div class="relative flex flex-col gap-7 sm:flex-row sm:items-start sm:justify-between">
                    <div class="max-w-2xl">
                        <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/12 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.12em] text-orca-100">
                            <x-filament::icon icon="heroicon-m-sparkles" class="h-4 w-4" />
                            {{ __('orcatech.upgrade.eyebrow') }}
                        </div>

                        <h2 class="text-3xl font-bold tracking-[-0.04em] sm:text-4xl">
                            {{ $this->featureName() }}
                        </h2>

                        <p class="mt-4 max-w-xl text-base leading-7 text-white/78 sm:text-lg">
                            {{ $this->featureDescription() }}
                        </p>
                    </div>

                    <div class="shrink-0 rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-wider text-orca-100/75">{{ __('orcatech.upgrade.available_in') }}</p>
                        <p class="mt-1 text-xl font-bold">{{ __('orcatech.packages.business.name') }}</p>
                        <p class="mt-1 text-sm text-white/70">{{ number_format((float) $business['price']) }} {{ __('orcatech.currency') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-8 p-6 sm:p-10 lg:grid-cols-[1fr_auto]">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.14em] text-orca-600 dark:text-orca-400">{{ __('orcatech.upgrade.benefits_label') }}</p>
                    <h3 class="mt-2 text-xl font-bold text-gray-950 dark:text-white">{{ __('orcatech.upgrade.benefits_title') }}</h3>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        @foreach ($businessFeatures as $perk => $definition)
                            <div class="flex items-start gap-3 rounded-xl border border-gray-100 bg-gray-50/80 p-3.5 dark:border-gray-800 dark:bg-gray-900/60">
                                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-orca-100 text-orca-700 dark:bg-orca-500/15 dark:text-orca-300">
                                    <x-filament::icon icon="heroicon-m-check" class="h-4 w-4" />
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __("orcatech.features.{$perk}.name") }}</p>
                                    <p class="mt-0.5 text-xs leading-5 text-gray-500 dark:text-gray-400">{{ __("orcatech.features.{$perk}.description") }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex min-w-[15rem] flex-col justify-center rounded-2xl border border-orca-100 bg-orca-50 p-5 dark:border-orca-900/50 dark:bg-orca-950/25">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orca-600 text-white shadow-lg shadow-orca-600/20">
                        <x-filament::icon icon="heroicon-o-rocket-launch" class="h-6 w-6" />
                    </div>
                    <p class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">{{ __('orcatech.upgrade.ready_title') }}</p>
                    <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">{{ __('orcatech.upgrade.ready_description') }}</p>

                    <x-filament::button
                        class="mt-5 w-full"
                        color="primary"
                        size="lg"
                        icon-position="after"
                        icon="heroicon-m-arrow-right-circle"
                        wire:click="switchToBusiness"
                    >
                        {{ __('orcatech.upgrade.cta_button') }}
                    </x-filament::button>

                    <p class="mt-3 text-center text-xs leading-5 text-gray-500 dark:text-gray-500">
                        {{ __('orcatech.upgrade.cta_hint') }}
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-100 bg-gray-50/70 px-6 py-4 text-center text-xs leading-5 text-gray-500 dark:border-gray-800 dark:bg-gray-900/50 dark:text-gray-500">
                {{ __('orcatech.enterprise_note') }}
            </div>
        </div>
    </div>
</x-filament-panels::page>
