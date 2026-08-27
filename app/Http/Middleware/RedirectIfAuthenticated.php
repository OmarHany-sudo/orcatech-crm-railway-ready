<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /** @var array<string, string> */
    protected array $roleRedirects = [
        'admin' => '/app',
        'manager' => '/app',
        'sales_rep' => '/app',
        'super_admin' => '/app',
        'free' => '/app',
        'staff' => '/staff',
        'buyer' => '/buyer',
        'seller' => '/seller',
        'tenant' => '/tenant',
        'landlord' => '/landlord',
        'contractor' => '/contractor',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = $guards === [] ? [null] : $guards;

        foreach ($guards as $guard) {
            if (! Auth::guard($guard)->check()) {
                continue;
            }

            $user = Auth::guard($guard)->user();
            $redirect = RouteServiceProvider::HOME;

            foreach ($this->roleRedirects as $role => $destination) {
                if ($user->hasRole($role)) {
                    $redirect = $destination;
                    break;
                }
            }

            return redirect()->to($redirect);
        }

        return $next($request);
    }
}
