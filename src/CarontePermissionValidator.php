<?php

/*
* GR
* VER 2.5.0
* 23.03.28
*/

namespace App\Classes\Vendor\Caronte;

class CarontePermissionValidator
{
    public static function hasApplication(string $application_id = null): bool
    {
        $uri_application = CaronteHelper::getUriApplication();
        $user            = CaronteHelper::getUser();

        foreach ($user->roles as $user_role) {
            if ($uri_application == $user_role->uri_application) {
                return true;
            }
        }
        return false;
    }

    public static function hasRoles(mixed $roles, string $application_id = null): bool
    {

        $uri_application = CaronteHelper::getUriApplication($application_id);
        $user            = CaronteHelper::getUser();

        if (!is_array($roles)) {
            $roles = explode(",", $roles);
        }

        $roles = array_map('trim', $roles);
        $roles[] = 'root';  //* root role is always available

        if (in_array('_self', $roles) && CaronteHelper::getRouteUser() == $user->uri_user) {
            return true;
        }

        foreach ($roles as $required_role) {
            foreach ($user->roles as $user_role) {
                if ($required_role == $user_role->name && $uri_application == $user_role->uri_application) {
                    return true;
                }
            }
        }

        return false;
    }
}
