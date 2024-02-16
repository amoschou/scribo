<?php

namespace AMoschou\Scribo;

class Scribo
{
    public static function config_arr($key, $separator = '|')
    {
        if ($config === '' || is_null($config)) {
            $config = [];
        }

        if (is_string($config)) {
            $config = explode($separator, $config);
        }

        if (is_array($config)) {
            return $config;
        }

        return $config;
    }
}
