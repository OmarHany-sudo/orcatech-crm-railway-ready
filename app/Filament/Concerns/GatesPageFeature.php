<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use App\Support\OrcaTech\Feature;

/**
 * Blocks a Filament page while its OrcaTech demo feature is locked in the
 * active package. The OrcaTechFeatureGate middleware normally redirects
 * direct URL access to the polished upgrade preview first; this trait keeps
 * Livewire hydrate/mount calls closed as defense in depth.
 */
trait GatesPageFeature
{
    public static function canAccess(): bool
    {
        return Feature::available(static::$orcaTechPageFeature) && parent::canAccess();
    }
}
