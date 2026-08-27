<div class="orcatech-advanced-analytics">
    @if ($locked)
        <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-gradient-to-br from-gray-50 to-gray-100 p-6 dark:border-gray-700/60 dark:from-gray-900 dark:to-gray-800/50">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-4">
                    <div class="pointer-events-none flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gray-200 text-gray-400 blur-[1px] dark:bg-gray-800 dark:text-gray-600">
                        <x-filament::icon icon="heroicon-o-chart-pie" class="h-6 w-6" />
                    </div>

                    <div>
                        <h3 class="text-base font-semibold text-gray-500 dark:text-gray-400">
                            {{ __('orcatech.dashboard.advanced_analytics') }}
                            <x-filament::icon icon="heroicon-m-lock-closed" class="inline-block h-4 w-4 align-[-2px]" />
                        </h3>

                        <p class="mt-1 max-w-lg text-sm text-gray-400 dark:text-gray-500">
                            {{ __('orcatech.dashboard.analytics_locked') }}
                        </p>
                    </div>
                </div>

                <x-filament::button
                    tag="a"
                    :href="\App\Http\Middleware\OrcaTechFeatureGate::upgradeUrl('advanced_reports')"
                    color="gray"
                    size="sm"
                >
                    {{ __('orcatech.upgrade.cta_button') }}
                </x-filament::button>
            </div>

            <div class="mt-5 grid grid-cols-2 gap-3 opacity-40 select-none sm:grid-cols-4" aria-hidden="true">
                @foreach ([92, 74, 88, 65] as $bar)
                    <div class="rounded-lg border border-dashed border-gray-300 p-4 dark:border-gray-700">
                        <div class="mb-2 h-2 w-10 rounded bg-gray-300 dark:bg-gray-700"></div>
                        <div class="h-3 w-full overflow-hidden rounded bg-gray-200 dark:bg-gray-800">
                            <div class="h-full rounded bg-gradient-to-r from-gray-300 to-gray-400 dark:from-gray-700 dark:to-gray-600" style="width: {{ $bar }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="rounded-2xl border border-sky-200 bg-white p-6 shadow-sm dark:border-sky-900/50 dark:bg-gray-900">
            <div class="mb-5 flex items-center justify-between gap-3">
                <h3 class="flex items-center gap-2 text-base font-semibold text-gray-900 dark:text-white">
                    <x-filament::icon icon="heroicon-o-chart-pie" class="h-5 w-5 text-sky-500" />
                    {{ __('orcatech.dashboard.advanced_analytics') }}
                </h3>

                <span class="inline-flex items-center gap-1 rounded-full bg-sky-100 px-2.5 py-0.5 text-xs font-semibold text-sky-700 dark:bg-sky-500/10 dark:text-sky-300">
                    <x-filament::icon icon="heroicon-m-sparkles" class="h-3.5 w-3.5" /> {{ __('orcatech.dashboard.business_insights') }}
                </span>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                <div class="md:col-span-2">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                        {{ __('orcatech.dashboard.stage_breakdown') }}
                    </p>

                    <div class="space-y-2.5">
                        @php($maxCount = max(1, collect($stageBreakdown)->max('count')))
                        @foreach ($stageBreakdown as $stage)
                            <div>
                                <div class="mb-1 flex items-center justify-between text-sm">
                                    <span class="font-medium capitalize text-gray-700 dark:text-gray-300">{{ $stage['name'] }}</span>
                                    <span class="tabular-nums text-gray-500 dark:text-gray-400">
                                        {{ number_format($stage['count']) }} · {{ number_format($stage['value'], 0) }} {{ __('orcatech.currency') }}
                                    </span>
                                </div>

                                <div class="h-2.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                                    <div class="h-full rounded-full bg-gradient-to-r from-sky-400 to-sky-600" style="width: {{ round(($stage['count'] / $maxCount) * 100) }}%"></div>
                                </div>
                            </div>
                        @endforeach

                        @if ($stageBreakdown === [])
                            <p class="text-sm text-gray-400">{{ __('orcatech.dashboard.no_activities') }}</p>
                        @endif
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="rounded-xl bg-emerald-50 p-4 dark:bg-emerald-500/10">
                        <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                            {{ __('orcatech.dashboard.win_rate') }}
                        </p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-emerald-700 dark:text-emerald-300">
                            {{ $winRate }}%
                        </p>
                    </div>

                    <div class="rounded-xl bg-indigo-50 p-4 dark:bg-indigo-500/10">
                        <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">
                            {{ __('orcatech.dashboard.avg_deal_size') }}
                        </p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-indigo-700 dark:text-indigo-300">
                            {{ number_format($avgDealSize, 0) }} {{ __('orcatech.currency') }}
                        </p>
                    </div>

                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800/60">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                            {{ __('orcatech.dashboard.lead_sources') }}
                        </p>

                        <ul class="space-y-1.5">
                            @foreach (collect($leadSources)->take(4) as $source)
                                <li class="flex items-center justify-between text-sm">
                                    <span class="capitalize text-gray-600 dark:text-gray-300">{{ $source['source'] }}</span>
                                    <span class="font-semibold tabular-nums text-gray-900 dark:text-white">{{ number_format($source['count']) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
