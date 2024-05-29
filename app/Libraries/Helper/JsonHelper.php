<?php

namespace App\Libraries\Helper;

use Illuminate\Support\Str;

class JsonHelper
{
    public static function isJSON($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && ( json_last_error() == JSON_ERROR_NONE ) ? true : false;
    }
}