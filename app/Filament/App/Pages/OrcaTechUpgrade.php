<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Support\OrcaTech\Feature;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Panel;

class OrcaTechUpgrade extends Page
{
    public ?string $feature = null;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'orcatech-upgrade';

    protected string $view = 'filament.app.pages.orcatech-upgrade';

    public static function getRoutePath(Panel $panel): string
    {
        return '/'.static::getSlug($panel).'/{feature?}';
    }

    public function mount(?string $feature = null): void
    {
        if ($feature !== null && isset(Feature::features()[$feature])) {
            $this->feature = $feature;
        }

        if ($this->feature !== null && Feature::available($this->feature)) {
            $this->redirect(self::dashboardUrl(), navigate: true);

            return;
        }
    }

    public function getTitle(): string
    {
        return (string) __('orcatech.upgrade.title');
    }

    public function featureName(): string
    {
        if ($this->feature === null) {
            return (string) __('orcatech.upgrade.default_feature');
        }

        return (string) __("orcatech.features.{$this->feature}.name");
    }

    public function featureDescription(): string
    {
        if ($this->feature === null) {
            return (string) __('orcatech.upgrade.default_description');
        }

        return (string) __("orcatech.features.{$this->feature}.description");
    }

    public function businessPackage(): array
    {
        return Feature::package('business');
    }

    public function switchToBusiness(): void
    {
        Feature::switchPackage('business');

        $this->redirect(self::dashboardUrl(), navigate: true);
    }

    private static function dashboardUrl(): string
    {
        try {
            return route('filament.app.pages.dashboard', [
                'tenant' => Filament::getTenant()?->getRouteKey(),
            ]);
        } catch (\Throwable) {
            return url('/app');
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('switchToBusiness')
                ->label(__('orcatech.upgrade.cta', [
                    'package' => Feature::package('business')['name'],
                    'price' => number_format((float) Feature::package('business')['price']),
                ]))
                ->icon('heroicon-m-arrow-right-circle')
                ->color('sky')
                ->size('lg')
                ->action(fn () => $this->switchToBusiness()),
        ];
    }
}
