<?php

namespace App\Components;

class Helper
{
    public static function fixEncoding($string)
    {
        return iconv('UTF-8', 'UTF-8//IGNORE', $string);
    }
}
