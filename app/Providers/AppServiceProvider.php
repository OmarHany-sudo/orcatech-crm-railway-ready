<?php

declare(strict_types=1);

namespace App\Providers;

use App\Console\Commands\BackupTeam;
use App\Console\Commands\CloneTeam;
use App\Console\Commands\ImportTeam;
use App\Console\Commands\MakeModuleCommand;
use App\Console\Commands\ModuleAutoloadCommand;
use App\Console\Commands\ModuleCommand;
use App\Console\Commands\NotifyOverdueTasks;
use App\Console\Commands\PublishScheduledPosts;
use App\Console\Commands\RestoreTeam;
use App\Console\Commands\SendReminders;
use App\Console\Commands\UpdatePostAnalytics;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\Task;
use App\Models\User;
use App\Models\Team;
use App\Console\Commands\DbSeed;
use App\Modules\ModuleManager;
use App\Modules\ModuleServiceProvider;
use App\Observers\AuditObserver;
use App\Support\SsoLogoutState;
use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Database\Console\Migrations\InstallCommand;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use Liberu\Foundation\ModuleManager\ModuleDiscovery;
use Liberu\Foundation\ModuleManager\ModuleManagerServiceProvider;
use Liberu\Foundation\ModuleManager\ModuleRegistry;
use Liberu\Foundation\Observability\ObservabilityServiceProvider;
use Liberu\Foundation\Search\SearchServiceProvider;
use Liberu\Foundation\Settings\SettingsServiceProvider;
use Liberu\Foundation\Theme\Providers\ThemeServiceProvider;
use Stancl\Tenancy\Commands\Seed as TenancySeed;

class AppServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([FreshCommand::class, MigrateCommand::class, InstallCommand::class]);
            $this->commands([
                BackupTeam::class,
                CloneTeam::class,
                ImportTeam::class,
                MakeModuleCommand::class,
                ModuleAutoloadCommand::class,
                ModuleCommand::class,
                NotifyOverdueTasks::class,
                PublishScheduledPosts::class,
                RestoreTeam::class,
                SendReminders::class,
                UpdatePostAnalytics::class,
            ]);

            // stancl/tenancy's seed command inherits Laravel 13's signature-based
            // SeedCommand, which drops the trait-injected --tenants option its
            // handle() relies on and silently skips central seeding on fresh
            // installs. Swap in a fixed variant at resolution time.
            $this->app->extend(TenancySeed::class, fn ($command, $app): DbSeed => new DbSeed($app['db']));
        }

        $this->app->singleton(ModuleManager::class, fn (): ModuleManager => new ModuleManager());
        // Request-scoped holder for the SSO single-logout redirect URL.
        $this->app->singleton(SsoLogoutState::class);
        $this->app->singleton(ModuleRegistry::class, fn (): ModuleRegistry => (new ModuleDiscovery())->discover([base_path('modules')]));
        $this->app->register(ModuleServiceProvider::class);
        $this->app->register(ModuleManagerServiceProvider::class);
        $this->app->register(SettingsServiceProvider::class);
        $this->app->register(SearchServiceProvider::class);
        $this->app->register(ObservabilityServiceProvider::class);
        $this->app->register(ThemeServiceProvider::class);
    }

    public function boot(): void
    {
        Gate::before(function (?User $user, string $ability): ?bool {
            if (! $user?->isDemoUser()) {
                return null;
            }

            $blockedAbilities = [
                'delete', 'deleteAny', 'forceDelete', 'forceDeleteAny',
                'restore', 'restoreAny', 'replicate',
                'manage_users', 'manage_roles', 'manage_permissions',
                'updateUserPassword', 'updateProfileInformation',
            ];

            return in_array($ability, $blockedAbilities, true) ? false : null;
        });

        Jetstream::useTeamModel(Team::class);

        // Audit core tenant models. Never observe AuditLog itself -> infinite recursion.
        foreach ([Contact::class, Deal::class, Lead::class, Opportunity::class, Task::class] as $model) {
            $model::observe(AuditObserver::class);
        }
    }
}
