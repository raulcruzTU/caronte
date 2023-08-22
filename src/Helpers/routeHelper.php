<?php

/**
 * GR
 * V1.3.5
 * 20.05.22
 */

function isAPI()
{
    if (\Request::is('api/*'))   return true;
    else                          return false;
}

function isHook()
{
    if (\Request::is('hooks/*'))   return true;
    else                           return false;
}

function isAdmin()
{
    if (\Request::is('admin/*'))   return true;
    else                            return false;
}

function getAccessType()
{
    if (isAPI())
        return "A";

    if (isHook())
        return "H";

    return "U";
}

function getAccessTypeId()
{

    switch (getAccessType()) {
        case "A":
            return \Request::get('token')->token;
            break;
        case "H":
            return \Request::get('hook_name') ? \Request::get('hook_name')[0] : 'unknown_hook';
            break;
        case "U";
            return \Request::get('user')->uri_user;
            break;
        default:
            return "X";
    }
}

//* Response functions
function notFound(string $message, string $forward_url = null)
{
    if (isAPI() || isHook()) {
        return Response($message, 404);
    } else {
        if (is_null($forward_url)) {
            return back()->withErrors([$message])->withInput();
        }

        return redirect($forward_url)->withErrors([$message])->withInput();
    }
}

function conflict(string $message, string $forward_url = null)
{
    if (isAPI() || isHook()) {
        return Response($message, 409);
    } else {
        if (is_null($forward_url)) {
            return back()->withErrors([$message])->withInput();
        }

        return redirect($forward_url)->withErrors([$message])->withInput();
    }
}

function badRequest(string $message, string $forward_url = null)
{
    if (isAPI() || isHook()) {
        return Response($message, 400);
    } else {
        if (is_null($forward_url)) {
            return back()->withErrors([$message])->withInput();
        }

        return redirect($forward_url)->withErrors([$message])->withInput();
    }
}

function forbidden(string $message, string $forward_url = null)
{
    if (isAPI() || isHook()) {
        return Response($message, 403);
    } else {
        if (is_null($forward_url)) {
            return back()->withErrors([$message])->withInput();
        }

        return redirect($forward_url)->withErrors([$message])->withInput();
    }
}
