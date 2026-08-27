<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\OrcaTech\Feature;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects direct URL access to Filament resources/pages whose slug is
 * protected by a locked OrcaTech demo feature to the polished upgrade page
 * instead of a bare 403.
 */
class OrcaTechFeatureGate
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();

        if ($route !== null && is_string($name = $route->getName())) {
            $slug = $this->protectedSlugFromRouteName($name);

            if ($slug !== null) {
                $feature = Feature::featureForSlug($slug);

                if ($feature !== null && ! Feature::available($feature)) {
                    return redirect()->to(self::upgradeUrl($feature));
                }
            }
        }

        return $next($request);
    }

    /**
     * Filament route names follow filament.{panel}.resources.{slug}.{page}
     * and filament.{panel}.pages.{slug}; slugs are kebab-cased, matching
     * the route_slugs entries in config/orcatech.php.
     */
    private function protectedSlugFromRouteName(string $name): ?string
    {
        if (preg_match('#^filament\.[^.]+\.(?:resources|pages)\.(?<slug>[^.\[]+)#', $name, $matches)) {
            return $matches['slug'];
        }

        return null;
    }

    public static function upgradeUrl(string $feature): string
    {
        try {
            $parameters = ['feature' => $feature];

            if (\Filament\Facades\Filament::getPanel('app')->hasTenancy()) {
                $parameters['tenant'] = \Filament\Facades\Filament::getTenant()?->getRouteKey();
            }

            return route('filament.app.pages.orcatech-upgrade', array_filter($parameters));
        } catch (\Throwable) {
            return url('/app/orcatech-upgrade?feature='.urlencode($feature));
        }
    }
}
