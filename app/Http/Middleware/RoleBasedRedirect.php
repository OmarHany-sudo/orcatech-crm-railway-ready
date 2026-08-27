<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleBasedRedirect
{
    /**
     * Legacy role destinations are kept for compatibility, while CRM roles
     * intentionally converge on the single tenant-aware application panel.
     *
     * @var array<string, string>
     */
    protected array $roleRedirects = [
        'super_admin' => '/app',
        'admin' => '/app',
        'manager' => '/app',
        'sales_rep' => '/app',
        'staff' => '/staff',
        'buyer' => '/buyer',
        'seller' => '/seller',
        'tenant' => '/tenant',
        'landlord' => '/landlord',
        'contractor' => '/contractor',
    ];

    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check() || $this->isInTenantContext($request) || $this->isFilamentRequest($request)) {
            return $next($request);
        }

        $redirect = $this->destinationForAuthenticatedUser();

        if ($redirect !== null && $this->isGuestEntryRequest($request)) {
            return redirect()->to($redirect);
        }

        return $next($request);
    }

    protected function destinationForAuthenticatedUser(): ?string
    {
        $user = Auth::user();

        foreach ($this->roleRedirects as $role => $redirect) {
            if ($user->hasRole($role)) {
                return $redirect;
            }
        }

        // Unknown roles should not manufacture a URL such as /role_name.
        // The CRM remains the safe canonical destination.
        return '/app';
    }

    protected function isFilamentRequest(Request $request): bool
    {
        $routeName = $request->route()?->getName();

        return is_string($routeName) && str_starts_with($routeName, 'filament.');
    }

    protected function isInTenantContext(Request $request): bool
    {
        return $request->segment(1) === 'tenant' || $request->is('tenant/*');
    }

    protected function isGuestEntryRequest(Request $request): bool
    {
        return $request->is('/')
            || $request->is('login')
            || $request->is('register');
    }
}
