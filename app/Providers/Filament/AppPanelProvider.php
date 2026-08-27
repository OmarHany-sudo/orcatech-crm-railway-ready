<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\App\Pages;
use App\Filament\App\Pages\EditProfile;
use App\Filament\App\Pages\OrcaTechAddons;
use App\Filament\App\Widgets\Home\AdvancedAnalytics;
use App\Filament\App\Widgets\Home\CrmPipelineStats;
use App\Filament\App\Widgets\Home\OrcaTechDemoBanner;
use App\Filament\App\Widgets\Home\RecentActivities;
use App\Filament\App\Widgets\Home\UpcomingTasks;
use App\Http\Middleware\EnsureSsoWhenRequired;
use App\Http\Middleware\OrcaTechFeatureGate;
use App\Http\Middleware\TeamsPermission;
use App\Listeners\CreatePersonalTeam;
use App\Listeners\SwitchTeam;
use App\Models\Team;
use App\Support\OrcaTech\Feature as OrcaTechFeature;
use App\Support\ThemeColors;
use Filament\Events\Auth\Registered;
use Filament\Events\TenantSet;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Jetstream\Features;
use Liberu\Foundation\Localization\Http\Middleware\SetLocale;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel
            ->default()
            ->id('app')
            ->path('app')
            // ->login([AuthenticatedSessionController::class, 'create'])
            // ->registration()
            // ->passwordReset()
            // ->emailVerification()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors(app(ThemeColors::class)->forSite())
            ->brandName(fn (): string => (string) __('orcatech.brand.name'))
            ->brandLogo(new HtmlString(Blade::render('<x-orcatech-logo class="h-8 w-auto" />')))
            ->brandLogoHeight('2rem')
            ->favicon(asset('orcatech/favicon.svg'))
            ->userMenuItems([
                MenuItem::make()
                    ->label('Profile')
                    ->icon('heroicon-o-user-circle')
                    ->url(fn () => $this->shouldRegisterMenuItem()
                        ? url(EditProfile::getUrl())
                        : url($panel->getPath())),
            ])
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                Dashboard::class,
                EditProfile::class,
                OrcaTechAddons::class,
                Pages\OrcaTechUpgrade::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets/Home'), for: 'App\\Filament\\App\\Widgets\\Home')
            ->widgets([
                Widgets\AccountWidget::class,
                OrcaTechDemoBanner::class,
                CrmPipelineStats::class,
                UpcomingTasks::class,
                RecentActivities::class,
                AdvancedAnalytics::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureSsoWhenRequired::class,
                TeamsPermission::class,
                OrcaTechFeatureGate::class,
            ]);

        $this->registerLockedNavigationItems($panel);

        $panel->renderHook(PanelsRenderHook::TOPBAR_END, function () {
            if (! auth()->check()) {
                return '';
            }

            return view('filament.orcatech.topbar-controls')->render();
        });

        // if (Features::hasApiFeatures()) {
        //     $panel->userMenuItems([
        //         MenuItem::make()
        //             ->label('API Tokens')
        //             ->icon('heroicon-o-key')
        //             ->url(fn () => $this->shouldRegisterMenuItem()
        //                 ? url(Pages\ApiTokenManagerPage::getUrl())
        //                 : url($panel->getPath())),
        //     ]);
        // }

        if (Features::hasTeamFeatures()) {
            $panel
                ->tenant(Team::class, ownershipRelationship: 'team')
                ->tenantRegistration(Pages\CreateTeam::class)
                ->tenantProfile(Pages\EditTeam::class)
                ->userMenuItems([
                    MenuItem::make()
                        ->label('Team Settings')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->url(fn () => $this->shouldRegisterMenuItem()
                            ? url(Pages\EditTeam::getUrl())
                            : url($panel->getPath())),
                ]);
        }

        return $panel;
    }

    /**
     * Business-only features stay visible in the navigation of every demo
     * package. When locked, their navigation entry becomes a polished
     * placeholder that opens the upgrade preview page.
     */
    private function registerLockedNavigationItems(Panel $panel): void
    {
        $items = [
            'workflow_automation' => ['icon' => 'heroicon-o-cpu-chip', 'group' => 'Automation', 'sort' => 61],
            'advanced_reports' => ['icon' => 'heroicon-o-chart-pie', 'group' => 'Analytics', 'sort' => 62],
            'data_import' => ['icon' => 'heroicon-o-arrow-down-tray', 'group' => 'Administration', 'sort' => 63],
            'integrations' => ['icon' => 'heroicon-o-link', 'group' => 'Communications', 'sort' => 64],
            'marketing_suite' => ['icon' => 'heroicon-o-megaphone', 'group' => 'Marketing', 'sort' => 65],
            'advertising' => ['icon' => 'heroicon-o-magnifying-glass-circle', 'group' => 'Analytics', 'sort' => 66],
            'territories' => ['icon' => 'heroicon-o-map-pin', 'group' => 'Sales', 'sort' => 67],
            'security_suite' => ['icon' => 'heroicon-o-shield-check', 'group' => 'Administration', 'sort' => 68],
        ];

        foreach ($items as $feature => $meta) {
            $panel->navigationItems([
                NavigationItem::make("orcatech-locked-{$feature}")
                    ->label(fn (): string => (string) __("orcatech.features.{$feature}.name"))
                    ->icon($meta['icon'])
                    ->group($meta['group'])
                    ->sort($meta['sort'])
                    ->visible(fn (): bool => ! OrcaTechFeature::available($feature))
                    ->url(fn (): string => OrcaTechFeatureGate::upgradeUrl($feature))
                    ->badge(fn (): string => (string) __('orcatech.locked.badge'), color: 'gray'),
            ]);
        }
    }

    public function boot(): void
    {
        /**
         * Keep Jetstream routes enabled for team management features.
         */
        // Jetstream::$registersRoutes = false;

        /**
         * Listen and create personal team for new accounts.
         */
        Event::listen(
            Registered::class,
            CreatePersonalTeam::class,
        );

        /**
         * Listen and switch team if tenant was changed.
         */
        Event::listen(
            TenantSet::class,
            SwitchTeam::class,
        );
    }

    public function shouldRegisterMenuItem(): bool
    {
        // Only register tenant-scoped menu items (Profile/Team Settings) when a
        // tenant is set — otherwise their getUrl() throws on the tenant-less
        // /app/new registration route (UrlGenerationException, missing {tenant}).
        return (bool) (auth()->user()?->hasVerifiedEmail() && Filament::hasTenancy() && Filament::getTenant());
    }
}
