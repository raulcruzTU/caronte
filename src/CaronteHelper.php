<?php

/**
 * GR
 * VER 2.5.0A
 * 23.03.28
 */

namespace Caronte\Auth;

use stdClass;
use Exception;

class CaronteHelper
{
    public static function getUser(): stdClass
    {
        $user = request()->get('user');

        if (is_null($user)) {
            throw new Exception('No user provided');
        }

        return $user;
    }

    public static function getUriApplication(string $application_id = null): string
    {
        if (is_null($application_id)) {
            return sha1(config('caronte.APP_ID'));
        }

        return sha1($application_id);
    }

    public static function getRouteUser(): string
    {
        return request()->route('uri_user');
    }

    public static function getFailURL(): string
    {
        return config('caronte.LOGIN_URL') . '?callback_url=' . base64_encode(request()->url());
    }
}
