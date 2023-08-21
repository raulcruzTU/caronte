<?php

/*
* GR
* VER 2.5.0
* 23.03.28
*/

namespace App\Classes\Vendor\Caronte;

class CaronteValidationResponse
{
    public function __construct(public mixed $user, public array $headers)
    {
    }
}
