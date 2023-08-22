<?php

/*
* GR
* VER 2.5.0
* 23.03.28
*/

namespace App\Http\Middleware\Caronte;

use Caronte\Auth\CaronteHelper;
use Caronte\Auth\CarontePermissionValidator;
use Exception;
use Closure;


class CaronteValidateApplication
{
    public function handle($request, Closure $next)
    {
        try {
            if (CarontePermissionValidator::hasApplication()) {
                return $next($request);
            } else {
                return forbidden('El usuario no tiene permisos para esta aplicación', CaronteHelper::getFailURL());
            }
        } catch (Exception $e) {
            return forbidden($e->getMessage());
        }
    }
}
