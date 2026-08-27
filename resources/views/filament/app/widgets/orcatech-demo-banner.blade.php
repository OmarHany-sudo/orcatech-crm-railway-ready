<div class="orcatech-demo-banner">
    @php($isBusiness = $package === 'business')
    <div class="rounded-2xl border p-5 {{
        $isBusiness
            ? 'border-sky-200 bg-gradient-to-r from-sky-50 to-indigo-50 dark:border-sky-900/50 dark:from-sky-950/40 dark:to-indigo-950/30'
            : 'border-emerald-200 bg-gradient-to-r from-emerald-50 to-teal-50 dark:border-emerald-900/50 dark:from-emerald-950/40 dark:to-teal-950/30'
    }}">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 items-start gap-3">
                <span class="mt-0.5 inline-flex h-8 shrink-0 items-center gap-2 whitespace-nowrap rounded-full px-3 text-xs font-bold uppercase tracking-wide {{
                    $isBusiness
                        ? 'bg-sky-600 text-white'
                        : 'bg-emerald-600 text-white'
                }}">
                    {{ __('orcatech.switcher.demo') }} · {{ __("orcatech.packages.{$package}.name") }}
                </span>

                <p class="min-w-0 max-w-xl text-sm leading-relaxed text-gray-600 dark:text-gray-300">
                    {{ $isBusiness ? __('orcatech.demo_banner.welcome_business') : __('orcatech.demo_banner.welcome_starter', ['locked' => $lockedCount]) }}
                </p>
            </div>

            @unless ($isBusiness)
                <x-filament::button
                    tag="a"
                    :href="\App\Http\Middleware\OrcaTechFeatureGate::upgradeUrl('workflow_automation')"
                    color="sky"
                    size="sm"
                    icon="heroicon-m-sparkles"
                    icon-position="after"
                >
                    {{ __('orcatech.upgrade.cta_button') }} — {{ number_format((float) $business['price']) }} {{ __('orcatech.currency') }}
                </x-filament::button>
            @endunless
        </div>
    </div>
</div>
