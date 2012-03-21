<?php

namespace Qi\Http;

class Path
{
    const PATH_TRIM = ' /\\';

    public static function current()
    {
        if ( self::isCommandLine() ) {
            $path = self::fromCommandLine();
        }
        elseif ( isset($_SERVER['PATH_INFO']) ) {
            $path = $_SERVER['PATH_INFO'];
        }
        else {
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $path = self::removeBase($path);
        }

        return self::format($path);
    }

    public static function removeBase($path)
    {
        if ($path == $_SERVER['SCRIPT_NAME']) return '';

        if ( $base = self::base() ) {
          $base = preg_quote($base);
          $path = preg_replace("!^$base/?!", '', $path, 1);
        }
        return $path;
    }

    /**
    * @return string the path from where the php controller is running.
    *                empty when the PATHINFO_DIRNAME is not present at
    *                the beginning of REQUEST_URI
    */
    public static function base()
    {
        $dir = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);
        return strpos($_SERVER['REQUEST_URI'], $dir.'/') === 0 ? $dir : '';
    }

    public static function format($path)
    {
        $path = trim($path, self::PATH_TRIM);
        $path = preg_replace('!/+!', '/', $path); // collapse double slashs
        return urldecode($path);
    }

    public static function fromCommandLine()
    {
        if ($_SERVER['argc'] <= 1) return '';
        $args = array_slice($_SERVER['argv'], 1); // skip file path
        return implode('/', $args);
    }

    public static function isCommandLine()
    {
        return PHP_SAPI == 'cli';
    }

    /**
     * Fix parse_str to return the parsed variables as array
     */
    public static function parse_query_string($__PHP_QUERY_STRING__)
    {
        parse_str($__PHP_QUERY_STRING__);
        unset($__PHP_QUERY_STRING__);
        return get_defined_vars();
    }
}
