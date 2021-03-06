<?php

namespace Qi\Http;

class Header
{
    public static $CHARSET = 'utf-8'; // 'iso-8859-1'

    public static function header($header, $value = null)
    {
        if ( headers_sent() ) return;
        if ($value !== null) $header = "$header: $value";
        header($header);
    }

    /*
     * $v can be: IE6, IE7 or IE8
     * http://www.chromium.org/developers/how-tos/chrome-frame-getting-started
     */
    public static function gcf($v = 1)
    {
        static::header("X-UA-Compatible", "chrome=$v");
    }

    public static function content_type($type, $charset = null)
    {
        if (!$charset) $charset = static::$CHARSET;
        static::header("Content-Type", "$type; charset=$charset");
    }

    public static function html($charset = null)
    {
        static::content_type('text/html', $charset);
    }

    public static function plain($charset = null)
    {
        static::content_type('text/plain', $charset);
    }

    public static function text($charset = null)
    {
        static::plain($charset);
    }

    public static function xml($charset = null)
    {
        static::content_type('text/xml', $charset);
    }

    public static function json($charset = null)
    {
        static::content_type('application/json', $charset);
    }

    public static function location($url = null)
    {
        if (!$url) $url = $_SERVER['REQUEST_URI']; // refresh to the current page
        static::header("Location", $url);
        exit;
    }

    public static function refresh()
    {
        static::location();
    }

    public static function back()
    {
        static::location(static::backURI());
    }
    
    public static function backURI()
    {
        return @$_SERVER['HTTP_REFERER'] ?: $_SERVER['REQUEST_URI'];
    }

    public static function notFound()
    {
        $status = @$_SERVER['SERVER_PROTOCOL'] ?: 'HTTP/1.1';
        static::header("$status 404 Not Found");
    }

    public static function noCache()
    {
        static::header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        static::header('Pragma', 'no-cache');
        static::header('Expires',  '-1');
        //header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
    }

    /**
     * http://php.net/manual/pt_BR/features.http-auth.php
     * @static
     * @param $user
     * @param $passwd
     * @param string $realm
     */
    public static function authenticate($user, $passwd, $realm = 'Authorization Required')
    {
        if ( ! isset($_SERVER['PHP_AUTH_USER'])
            || $_SERVER['PHP_AUTH_USER'] != $user
            || $_SERVER['PHP_AUTH_PW'] != $passwd) {
            static::authorization_required($realm);
        }
    }

    public static function authorization_required($realm = 'Authorization Required')
    {
        static::header('WWW-Authenticate', "Basic realm='$realm'");
        static::header("$_SERVER[SERVER_PROTOCOL] 401 Unauthorized");
        die($realm);
    }
}
