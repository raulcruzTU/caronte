<?php

/*
* GR
* VER 2.5.0
* 23.03.28
*/

namespace Caronte\Auth;

class CaronteValidationResponse
{
    public function __construct(public mixed $user, public array $headers)
    {
    }
}
