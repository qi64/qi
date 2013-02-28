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
        elseif ( @$_SERVER['PATH_INFO'] ) {
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
    * Funciona tanto para /api/index.php, /api/ e /api
    */
    public static function base()
    {
        $dir = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);
        $uri = rtrim($_SERVER['REQUEST_URI'], '/');
        return strpos("$uri/", "$dir/") === 0 ? $dir : '';
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
        $path = array_shift($args);
        if ( in_array($path, array('GET', 'POST', 'PUT', 'DELETE')) ) {
            return array_shift($args);
        }
        return $path;
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
