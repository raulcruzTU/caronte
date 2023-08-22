<?php

return [
    'URL'            => env('CARONTE_URL', 'https://caronte.sistemas-teleurban.com/'),
    'TOKEN_KEY'      => env('CARONTE_TOKEN_KEY', 't3l3urb@n'),
    'ISSUER_ID'      => env('CARONTE_ISSUER_ID', 'https://caronte.teleurban.tv'),
    'APP_ID'         => env('CARONTE_APP_ID', 'com.femaseisa.crm'),
    'APP_SECRET'     => env('CARONTE_APP_SECRET', 'a662706690f2f56c55c06cc54ae36d6f3ad3ddb1'),
    'LOGIN_URL'      => env('CARONTE_LOGIN_URL', '/login'),
    'USE_2FA'        => env('CARONTE_2FA', false),
    'ENFORCE_ISSUER' => env('CARONTE_ENFORCE_ISSUER', false)
];
