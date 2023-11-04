<?php

declare(strict_types=1);
// If FNMATCH on System is not existent -> declare it
if (!function_exists('fnmatch')) {
    function fnmatch($pattern, $string)
    {
        return preg_match('#^' . strtr(preg_quote($pattern, '#'), ['\*' => '.*', '\?' => '.']) . '$#i', $string);
    }
}