<?php

/*
* GR
* VER 2.5.0
* 23.03.28
*/

namespace Caronte\Auth\Middleware;

use Caronte\Auth\CarontePermissionValidator;
use Exception;
use Closure;

class CaronteValidateRoles
{
    public function handle($request, Closure $next, ...$roles)
    {
        try {
            if (CarontePermissionValidator::hasRoles($roles)) {
                return $next($request);
            } else {
                return forbidden('Permisos Insuficientes');
            }
        } catch (Exception $e) {
            return forbidden($e->getMessage());
        }
    }
}
