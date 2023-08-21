<?php

/**
 * GR
 * VER 1.0.0
 * 18.04.14
 */

namespace App\Http\Middleware\Auth;

use Closure;

class ValidateRoles
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (userHasRoles($roles)) {
            return $next($request);
        } else {
            if (isAPI()) {
                return Response('Permisos insuficientes', 403);
            }

            return back()->with(['error' => 'Permisos Insuficientes']);
        }
    }
}
