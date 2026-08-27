<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Database\Console\Seeds\SeedCommand;
use Stancl\Tenancy\Commands\Seed as TenancySeed;

class DbSeed extends TenancySeed
{
    protected $signature = 'db:seed
        {class? : The class name of the root seeder}
        {--class=Database\\Seeders\\DatabaseSeeder : The class name of the root seeder}
        {--database= : The database connection to seed}
        {--force : Force the operation to run when in production}
        {--tenants=* : Tenant(s) to seed; defaults to every existing tenant}';

    public function handle(): void
    {
        foreach (config('tenancy.seeder_parameters', []) as $parameter => $value) {
            if (! $this->input->hasParameterOption($parameter)) {
                $this->input->setOption(ltrim((string) $parameter, '-'), $value);
            }
        }

        if (! $this->confirmToProceed()) {
            return;
        }

        // Fresh installs have no tenants yet; fall back to central-only seeding
        // instead of silently skipping it.
        if ($this->option('tenants') === [] && tenancy()->query()->count() === 0) {
            SeedCommand::handle();

            return;
        }

        parent::handle();
    }
}
