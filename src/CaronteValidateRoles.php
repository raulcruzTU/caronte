<?php

/*
* GR
* VER 2.5.0
* 23.03.28
*/

namespace App\Http\Middleware\Caronte;

use App\Classes\Vendor\Caronte\CarontePermissionValidator;
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
