<?php

/**
 * GR
 * V1.1.0
 * 19.05.02
 *
 *
 *
 */

function userHasRoles($roles, $app_id = '')
{

    if (empty(\Request::get('user')))    return FALSE;

    $user = \Request::get('user');

    if ($app_id == '')   $uri_application = sha1(config('caronte.APP_ID'));
    else                $uri_application = sha1($app_id);

    if (empty(request()->route('uri_user')))     $route_user = '';
    else                                        $route_user = request()->route('uri_user');

    if (!is_array($roles))   $roles = explode(",", $roles);

    $roles = array_map('trim', $roles);
    $roles[] = 'root';                      //Agregamos el rol root para que no sea necesario agregarlo en las config

    if (in_array('_self', $roles) && $route_user == $user->uri_user)
        return TRUE;

    foreach ($roles as $required_role) {
        foreach ($user->roles as $user_role) {
            if ($required_role == $user_role->name && $uri_application == $user_role->uri_application) {
                return TRUE;
            }
        }
    }

    return FALSE;
}

function userHasApplication($app_id = '')
{

    if (empty(\Request::get('user')))    return FALSE;

    $user            = \Request::get('user');

    if ($app_id == '')   $uri_application = sha1(config('caronte.APP_ID'));
    else                $uri_application = sha1($app_id);

    foreach ($user->roles as $user_role) {
        if ($uri_application == $user_role->uri_application) {
            return TRUE;
        }
    }
    return FALSE;
}

function webToken()
{

    if (Storage::disk('local')->exists('tokens/' . Cookie::get('token'))) {
        return Storage::disk('local')->get('tokens/' . Cookie::get('token'));
    } else {
        return "";
    }
}
