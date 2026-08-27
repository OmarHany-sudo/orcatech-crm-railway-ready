<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets\Home;

use App\Models\Task;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class UpcomingTasks extends TableWidget
{
    protected static bool $isLazy = false;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = null;

    public function getTableHeading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return (string) __('orcatech.dashboard.upcoming_tasks');
    }

    public function table(Table $table): Table
    {
        $teamId = auth()->user()?->currentTeam?->getKey();

        return $table
            ->query(fn () => Task::query()
                ->when($teamId !== null, fn ($q) => $q->where('team_id', $teamId))
                ->whereIn('status', ['pending', 'in_progress'])
                ->orderBy('due_date')
                ->limit(8))
            ->columns([
                TextColumn::make('name')
                    ->label((string) __('orcatech.dashboard.open_tasks'))
                    ->searchable()
                    ->weight('semibold'),
                TextColumn::make('status')
                    ->label((string) __('orcatech.dashboard.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => (string) __("orcatech.tasks.statuses.{$state}"))
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'in_progress',
                        'success' => 'completed',
                    ]),
                TextColumn::make('due_date')
                    ->label((string) __('orcatech.dashboard.due_date'))
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('assignedTo.name')
                    ->label((string) __('orcatech.dashboard.assigned_to')),
            ])
            ->paginated(false);
    }
}
