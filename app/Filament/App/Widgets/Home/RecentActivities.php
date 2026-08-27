<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets\Home;

use App\Models\Activity;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentActivities extends TableWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    public function getTableHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return (string) __('orcatech.dashboard.recent_activities');
    }

    public function table(Table $table): Table
    {
        $teamId = auth()->user()?->currentTeam?->getKey();

        return $table
            ->query(fn () => Activity::query()
                ->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))
                ->orderByDesc('date')
                ->limit(8))
            ->columns([
                TextColumn::make('type')
                    ->label((string) __('orcatech.dashboard.activity_type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => (string) __("orcatech.activities.types.{$state}")),
                TextColumn::make('description')
                    ->label((string) __('orcatech.dashboard.recent_activities'))
                    ->limit(70),
                TextColumn::make('outcome'),
                TextColumn::make('date')
                    ->label((string) __('orcatech.dashboard.date'))
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
