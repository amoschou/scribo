<?php

namespace AMoschou\Scribo;

class Scribo
{
    public static function configArr($key, $default = null, $separator = '|')
    {
        $config = config($key, $default) ?? [];

        return is_array($config) ? $config : explode($separator, $config);
    }
}
