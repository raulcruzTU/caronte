<?php

/*
* GR
* VER 2.5.0
* 23.03.28
*/

namespace Caronte\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Exception;
use Storage;

class CaronteRequest
{
    public static function userPasswordLogin(Request $request)
    {
        $callback_url  = base64_decode($request->callback_url);

        $validator = Validator::make(
            $request->all(),
            [
                'email'     => 'required|email',
                'password'  => 'required'
            ]
        );

        if ($validator->fails()) {
            return badRequest($validator->errors());
        }


        try {
            $caronte_response = HTTP::post(
                config('caronte.URL') . 'api/login',
                [
                    'email'     => $request->email,
                    'password'  => $request->password
                ]
            );

            if ($caronte_response->failed()) {
                throw new RequestException($caronte_response);
            }

            $token_str = $caronte_response->body();

            Caronte::validateToken(raw_token: $token_str);

            if (isAPI()) {
                return Response($token_str, 200);
            }

            $token_id = Str::random(20);

            Storage::disk('local')->put('tokens/' . $token_id, $token_str);
            Caronte::setCookie($token_id);
        } catch (Exception $e) {
            return forbidden($e->getMessage());
        }
        if (empty($callback_url)) {
            return view('modules.admin.dashboard')->with(
                [
                    'success' =>  'Sesión iniciada con éxito'
                ]
            );
        }
        return redirect($callback_url)
            ->with(
                [
                    'success' => 'Sesión iniciada con éxito'
                ]
            );
    }

    public static function twoFactorTokenRequest(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email'     => 'required|email',
            ]
        );

        if ($validator->fails()) {
            return badRequest($validator->errors());
        }

        try {
            $caronte_response = HTTP::post(
                config('caronte.URL') . 'api/2fa',
                [
                    'email'             => $request->email,
                    'callback_url'      => $request->callback_url,
                    'application_url'   => config('app.url')
                ]
            );

            if ($caronte_response->failed()) {
                throw new RequestException($caronte_response);
            }

            if (isAPI()) {
                return Response("Authentication email sent to " . $request->email, 200);
            }

            return view('caronte.2fa_request_success')->with(['email' => $request->email]);
        } catch (Exception $e) {
            return badRequest($e->getMessage());
        }
    }

    public static function twoFactorTokenLogin(Request $request, $token)
    {
        $callback_url = base64_decode($request->callback_url);

        try {
            $caronte_response = HTTP::get(config('caronte.URL') . 'api/2fa/' . $token);

            if ($caronte_response->failed()) {
                throw new RequestException($caronte_response);
            }

            $token_str = $caronte_response->body();

            Caronte::validateToken(raw_token: $token_str);

            if (isAPI()) {
                return Response($token_str, 200);
            }

            $token_id = Str::random(20);

            Storage::disk('local')->put('tokens/' . $token_id, $token_str);
            Caronte::setCookie($token_id);
        } catch (RequestException $e) {
            return forbidden($e->getMessage());
        }

        return redirect($callback_url)
            ->with(
                [
                    'success' => 'Sesión iniciada con éxito'
                ]
            );
    }


    public static function logout(Request $request, $logout_all_sessions = false)
    {
        $token_str = Caronte::webToken();

        try {
            $caronte_response = HTTP::withHeaders(
                [
                    'Authorization' => "Bearer " . $token_str
                ]
            )->get(config('caronte.URL') . 'api/logout' . ($logout_all_sessions ? 'All' : ''));

            if ($caronte_response->failed()) {
                throw new RequestException($caronte_response);
            }

            $response_array = ['success' => 'Sesión cerrada con éxito'];
        } catch (RequestException $e) {
            $response_array = ['error' => $e->getMessage()];
        }

        Caronte::forgetCookie();

        return Redirect(config('caronte.LOGIN_URL'))->with($response_array);
    }
}
