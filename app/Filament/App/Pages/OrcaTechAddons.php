<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use Filament\Pages\Page;

class OrcaTechAddons extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static string|\UnitEnum|null $navigationGroup = 'Account';

    protected static ?int $navigationSort = 90;

    protected string $view = 'filament.app.pages.orcatech-addons';

    public static function getNavigationLabel(): string
    {
        return (string) __('orcatech.addons_page.title');
    }

    public function getTitle(): string
    {
        return (string) __('orcatech.addons_page.title');
    }

    /** @return array<int, array{key: string, name: string, icon: string}> */
    public function addons(): array
    {
        return collect((array) config('orcatech.addons', []))
            ->map(fn (array $addon, string $key): array => [
                'key' => $key,
                'name' => (string) __("orcatech.addons.{$key}.name"),
                'description' => (string) __("orcatech.addons.{$key}.description"),
                'icon' => $addon['icon'] ?? 'heroicon-o-puzzle-piece',
            ])
            ->values()
            ->all();
    }
}
