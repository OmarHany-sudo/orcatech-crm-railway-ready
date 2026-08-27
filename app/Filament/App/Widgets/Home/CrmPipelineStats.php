<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets\Home;

use App\Models\Deal;
use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CrmPipelineStats extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = '';

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        $teamId = auth()->user()?->currentTeam?->getKey();

        $leadsQuery = Lead::query();
        $dealsQuery = Deal::query();

        if ($teamId !== null) {
            $leadsQuery->where('team_id', $teamId);
            $dealsQuery->where('team_id', $teamId);
        }

        $totalLeads = (int) (clone $leadsQuery)->count();
        $newLeads = (int) (clone $leadsQuery)->where('status', 'new')->count();
        $qualifiedLeads = (int) (clone $leadsQuery)->where('status', 'qualified')->count();

        $activeDeals = (int) (clone $dealsQuery)
            ->where(function ($query): void {
                $query->whereNotIn('stage', ['won', 'lost', 'closed'])->orWhereNull('stage');
            })
            ->count();

        $wonDeals = (int) (clone $dealsQuery)->whereIn('stage', ['won'])->count();
        $lostDeals = (int) (clone $dealsQuery)->whereIn('stage', ['lost'])->count();

        $pipelineValue = (float) (clone $dealsQuery)
            ->where(function ($query): void {
                $query->whereNotIn('stage', ['won', 'lost', 'closed'])->orWhereNull('stage');
            })
            ->sum('value');

        $decided = $wonDeals + $lostDeals;
        $conversionRate = $decided > 0 ? round(($wonDeals / $decided) * 100, 1) : 0.0;

        return [
            Stat::make((string) __('orcatech.dashboard.total_leads'), number_format($totalLeads))
                ->description((string) __('orcatech.dashboard.new_leads').': '.number_format($newLeads))
                ->icon('heroicon-o-user-plus')
                ->color('primary'),

            Stat::make((string) __('orcatech.dashboard.qualified_leads'), number_format($qualifiedLeads))
                ->description((string) __('orcatech.dashboard.conversion_rate').": {$conversionRate}%")
                ->icon('heroicon-o-funnel')
                ->color('info'),

            Stat::make((string) __('orcatech.dashboard.active_deals'), number_format($activeDeals))
                ->description(__('orcatech.dashboard.won_deals').' '.number_format($wonDeals).' · '.
                    __('orcatech.dashboard.lost_deals').' '.number_format($lostDeals))
                ->icon('heroicon-o-briefcase')
                ->color('success'),

            Stat::make(
                (string) __('orcatech.dashboard.pipeline_value'),
                number_format($pipelineValue, 0).' '.__('orcatech.currency'),
            )
                ->description((string) __('orcatech.dashboard.avg_deal_size').': '.
                    number_format($activeDeals > 0 ? $pipelineValue / $activeDeals : 0.0, 0))
                ->icon('heroicon-o-chart-bar')
                ->color('warning'),
        ];
    }
}
