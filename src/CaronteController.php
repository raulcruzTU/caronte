<?php

/*
* GR
* VER 2.5.0
* 23.03.28
*/

namespace Caronte\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
//Models
use Caronte\Auth\CaronteRequest;

class CaronteController extends Controller
{
    public function loginForm(Request $request)
    {
        $login_view = config('caronte.USE_2FA') ? '2fa_login' : 'login';

        return View('caronte.' . $login_view)
            ->with(
                [
                    'callback_url' => $request->callback_url
                ]
            );
    }

    public function login(Request $request)
    {
        if (config('caronte.USE_2FA')) {
            return CaronteRequest::twoFactorTokenRequest(request: $request);
        }
        return CaronteRequest::userPasswordLogin(request: $request);
    }

    public function logout(Request $request)
    {
        try {
            return CaronteRequest::logout(
                request: $request,
                logout_all_sessions: $request->filled('all')
            );
        } catch (Exception $e) {
            return badRequest($e->getMessage());
        }
    }

    public function twoFactorTokenLogin(Request $request, $token)
    {
        return CaronteRequest::twoFactorTokenLogin(request: $request, token: $token);
    }
}
