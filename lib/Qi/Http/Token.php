<?php

namespace Qi\Http;

class Token
{
    public static $NAME = 'csrf-token';

    public static function generate()
    {
        return md5( microtime().mt_rand() );
    }

    public static function get()
    {
        if ( ! isset($_SESSION[self::$NAME]) ) {
            $_SESSION[self::$NAME] = self::generate();
        }
        return self::session();
    }

    public static function session()
    {
        return @$_SESSION[self::$NAME];
    }

    public static function request()
    {
        return @$_REQUEST[self::$NAME];
    }

    public static function check($token = null)
    {
        if ( ! Method::isUnsafe() ) return;
        if (!$token) $token = self::request();
        if (self::session() != $token) throw new \DomainException("Invalid Authenticity Token '$token'. It should be '{self::session()}'");
    }
}
