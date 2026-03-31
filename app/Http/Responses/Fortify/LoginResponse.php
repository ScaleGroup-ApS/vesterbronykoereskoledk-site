<?php

namespace App\Http\Responses\Fortify;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fortify's default LoginResponse returns JSON when the request "wants JSON".
 * Some clients send "Accept: application/json" first; Inertia then receives JSON instead
 * of a redirect and never navigates to the dashboard. Inertia requests always include
 * the X-Inertia header, so we always redirect for those.
 */
class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        if ($request->header('X-Inertia')) {
            return redirect()->intended(Fortify::redirects('login'));
        }

        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->intended(Fortify::redirects('login'));
    }
}
