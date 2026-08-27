<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    protected function redirectPath(): string
    {
        // OrcaTech is one CRM application. Role differences are enforced by
        // permissions and tenancy, not by sending users to fake role portals.
        return '/app';
    }

    public function toResponse($request)
    {
        setPermissionsTeamId(Auth::user()->current_team_id);
        $redirect = $this->redirectPath();

        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false], 200)
            : redirect()->intended($redirect);
    }
}
