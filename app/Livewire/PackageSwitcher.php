<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Support\OrcaTech\Feature;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Livewire\Component;

final class PackageSwitcher extends Component
{
    public string $currentPackage = 'starter';

    /** @var array<string, array<string, mixed>> */
    public array $packages = [];

    public function mount(): void
    {
        $this->currentPackage = Feature::currentPackage();
        $this->packages = Feature::packages();
    }

    public function switchPackage(string $package): void
    {
        if (! isset($this->packages[$package]) || $package === $this->currentPackage) {
            return;
        }

        Feature::switchPackage($package);
        $this->currentPackage = $package;

        Session::flash('success', (string) __(
            $package === 'business' ? 'orcatech.switcher.activated_business' : 'orcatech.switcher.activated_starter',
            ['name' => $this->packages[$package]['name']],
        ));

        $referer = request()->header('Referer');
        $base = url('/');

        $this->redirect(is_string($referer) && str_starts_with($referer, $base) ? $referer : url('/app'));
    }

    public function render(): View
    {
        return view('livewire.package-switcher');
    }
}
