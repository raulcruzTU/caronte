<?php

/*
* GR
* VER 2.5.0
* 23.03.28
*/

namespace App\Http\Middleware\Caronte;

use App\Classes\Vendor\Caronte\Caronte as CaronteClass;
use App\Classes\Vendor\Caronte\CaronteHelper;
use Closure;
use Exception;

class Caronte
{
    public function handle($request, Closure $next)
    {
        if (isAPI()) {
            $raw_token = $request->bearerToken();
        } else {
            $raw_token = CaronteClass::webToken();
        }

        try {
            $validation_response = CaronteClass::validateToken(raw_token: $raw_token);
        } catch (Exception $e) {
            return forbidden($e->getMessage(), CaronteHelper::getFailURL());
        }

        $request->attributes->add(['user' => $validation_response->user]);
        $response = $next($request);

        foreach ($validation_response->headers as $header => $value) {
            $response->headers->set($header, $value);
        }

        return $response;
    }
}
