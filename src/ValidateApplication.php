<?php

/**
 * GR
 * VER 1.0.0
 * 18.04.19
 */

namespace App\Http\Middleware\Auth;

use Closure;

class ValidateApplication
{
    public function handle($request, Closure $next)
    {
        $fail_redirect = '/admin/login?callback_url=' . base64_encode($request->url());

        if (userHasApplication()) {
            return $next($request);
        } else {
            if (isAPI()) {
                return Response('El usuario no tiene permisos para esta aplicación', 403);
            } else {
                return Redirect($fail_redirect)
                    ->with(
                        [
                            'error' => 'El usuario no tiene permisos para esta aplicación'
                        ]
                    );
            }
        }
    }
}
