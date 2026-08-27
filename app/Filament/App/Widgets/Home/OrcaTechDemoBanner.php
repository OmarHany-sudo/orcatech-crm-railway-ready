<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets\Home;

use App\Support\OrcaTech\Feature;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class OrcaTechDemoBanner extends Widget
{
    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.app.widgets.orcatech-demo-banner';

    public function render(): View
    {
        return view($this->view, [
            'package' => Feature::currentPackage(),
            'lockedCount' => count(Feature::lockedFeatures()),
            'business' => Feature::package('business'),
        ]);
    }
}
