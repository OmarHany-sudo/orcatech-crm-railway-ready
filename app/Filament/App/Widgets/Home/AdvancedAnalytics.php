<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets\Home;

use App\Models\Deal;
use App\Support\OrcaTech\Feature;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class AdvancedAnalytics extends Widget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.app.widgets.advanced-analytics';

    public bool $locked = false;

    /** @var array<int, array{name: string, count: int, value: float}> */
    public array $stageBreakdown = [];

    /** @var array<int, array{source: string, count: int}> */
    public array $leadSources = [];

    public float $winRate = 0.0;

    public float $avgDealSize = 0.0;

    public function mount(): void
    {
        $this->locked = ! Feature::available('advanced_reports');

        if ($this->locked) {
            return;
        }

        $teamId = auth()->user()?->currentTeam?->getKey();

        $stages = Deal::query()
            ->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))
            ->selectRaw('COALESCE(stage, ?) as stage_label', ['prospect'])
            ->selectRaw('COUNT(*) as aggregate')
            ->selectRaw('SUM(value) as stage_value')
            ->groupBy('stage_label')
            ->orderByDesc('aggregate')
            ->get();

        $this->stageBreakdown = $stages
            ->map(fn ($row): array => [
                'name' => (string) __('orcatech.deals.stages.'.str_replace('-', '_', (string) $row->stage_label)),
                'count' => (int) $row->aggregate,
                'value' => (float) $row->stage_value,
            ])
            ->all();

        $sources = \App\Models\Lead::query()
            ->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))
            ->selectRaw("COALESCE(source, 'other') as source_label")
            ->selectRaw('COUNT(*) as aggregate')
            ->groupBy('source_label')
            ->orderByDesc('aggregate')
            ->get();

        $this->leadSources = $sources
            ->map(fn ($row): array => [
                'source' => (string) __('orcatech.leads.sources.'.(string) str($row->source_label)->replace('-', '_')),
                'count' => (int) $row->aggregate,
            ])
            ->all();

        $dealQuery = Deal::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId));
        $won = (int) (clone $dealQuery)->whereIn('stage', ['won'])->count();
        $lost = (int) (clone $dealQuery)->whereIn('stage', ['lost'])->count();
        $decided = $won + $lost;
        $this->winRate = $decided > 0 ? round(($won / $decided) * 100, 1) : 0.0;

        $avgQuery = Deal::query()->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId));
        $totalValue = (float) (clone $avgQuery)->sum('value');
        $totalDeals = (int) (clone $avgQuery)->count();
        $this->avgDealSize = $totalDeals > 0 ? round($totalValue / $totalDeals, 0) : 0.0;
    }

    public function render(): View
    {
        return view($this->view, ['data' => $this]);
    }
}
