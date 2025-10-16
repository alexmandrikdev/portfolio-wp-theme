<?php

defined('ABSPATH') || exit;

if (!function_exists('dump')) {
    function dump(...$vars)
    {
        echo '<pre>';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
    }
}

if (!function_exists('dd')) {
    function dd(...$vars)
    {
        dump(...$vars);
        die();
    }
}
