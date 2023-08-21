<?php

/*
* GR
* VER 2.5.0
* 23.03.28
*/

namespace App\Classes\Vendor\Caronte;

use Exception;

class CaronteKeyTool
{
    public static function padKey256($key)
    {
        $validSize = 32;

        $keySize = strlen($key);

        if ($keySize > $validSize) {
            throw new Exception("Key size is too large");
        }

        return str_pad($key, $validSize, "\0");
    }
}
