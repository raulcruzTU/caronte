<?php

/*
* GR
* VER 2.5.0
* 23.03.28
*/

namespace App\Classes\Vendor\Caronte;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Plain as PlainToken;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Exception;
use Storage;

class Caronte
{
    public const COOKIE_NAME = 'caronte_token';

    public static function validateToken(string $raw_token): CaronteValidationResponse
    {
        $token     = static::decodeToken(raw_token: $raw_token);
        $config    = static::getConfig();
        $validator = $config->validator();

        try {
            if (config('caronte.ENFORCE_ISSUER')) {
                $validator->assert($token, new IssuedBy(config('caronte.ISSUER_ID')));
            }
            $validator->assert($token, new SignedWith(...static::getSignerData()));
        } catch (RequiredConstraintsViolated $e) {
            throw new Exception($e->getMessage());
        }


        try {
            $validator->assert($token, new StrictValidAt(SystemClock::fromUTC()));
            $user    = json_decode($token->claims()->get('user'));
            $headers = [];
        } catch (RequiredConstraintsViolated $e) {
            $new_token_str  = static::exchangeToken(raw_token: $raw_token);
            $new_token      = static::decodeToken(raw_token: $new_token_str);

            $user = json_decode($new_token->claims()->get('user'));
            $headers = [
                'new_token' =>  $new_token_str
            ];
        }

        return new CaronteValidationResponse($user, $headers);
    }

    public static function exchangeToken(string $raw_token): string
    {
        try {
            $caronte_response = HTTP::withHeaders(
                [
                    'Authorization' => "Bearer " . $raw_token
                ]
            )->get(config('caronte.URL') . 'api/tokens/exchange');

            if ($caronte_response->failed()) {
                throw new RequestException($caronte_response);
            }

            $new_token_str = $caronte_response->body();

            if (!isAPI()) {
                Storage::disk('local')->put('tokens/' . Cookie::get('caronte_token'), $new_token_str);
            }

            return $new_token_str;
        } catch (RequestException $e) {
            static::forgetCookie();

            throw new Exception($e->getMessage());
        }
    }

    public static function decodeToken(string $raw_token): PlainToken
    {
        if ($raw_token == null) {
            throw new Exception('Token not provided');
        }

        if (count(explode(".", $raw_token)) != 3) {
            throw new Exception('Malformed Token');
        }

        $config = static::getConfig();
        $token  = $config->parser()->parse($raw_token);

        if (!$token->claims()->has('user')) {
            throw new Exception('Invalid token');
        }

        return $token;
    }

    public static function getSignerData()
    {
        return [
            new Sha256(),
            InMemory::plainText(
                CaronteKeyTool::padKey256(config('caronte.TOKEN_KEY'))
            )
        ];
    }

    public static function getConfig()
    {
        $config = Configuration::forSymmetricSigner(...static::getSignerData());
        return $config;
    }

    public static function setCookie(string $token_id): void
    {
        Cookie::queue(Cookie::forever(static::COOKIE_NAME, $token_id));
    }

    public static function forgetCookie(): void
    {
        if (Storage::disk('local')->exists('tokens/' . Cookie::get(static::COOKIE_NAME))) {
            Storage::disk('local')->delete('tokens/' . Cookie::get(static::COOKIE_NAME));
        }

        Cookie::queue(Cookie::forget(static::COOKIE_NAME));
    }

    public static function webToken(): string
    {
        if (Storage::disk('local')->exists('tokens/' . Cookie::get(static::COOKIE_NAME))) {
            return Storage::disk('local')->get('tokens/' . Cookie::get(static::COOKIE_NAME));
        } else {
            return "";
        }
    }

    private function updateUserData($user)
    {
        //!UPDATE FUNCTION TO USE UPDATEOR CREATE
        $local_user = User::find($user->uri_user);

        if (!$local_user) {
            $local_user = new User();

            $local_user->uri_user = $user->uri_user;
            $local_user->email    = $user->email;
        }

        $local_user->name = $user->name;
        $local_user->save();

        $local_user->metadata()->delete();

        foreach ($user->metadata as $metadata) {
            $local_user->metadata()->create([
                'uri_user'  => $user->uri_user,
                'key'       => $metadata->key,
                'value'     => $metadata->value
            ]);
        }
    }
}
