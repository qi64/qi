<?php

namespace Qi\Http;

class Method
{

    public static $IDEMPOTENT = array('GET', 'HEAD', 'OPTIONS', 'PUT', 'DELETE');
    public static $NOT_IDEMPOTENT = array('POST', 'PATCH');
    public static $SAFE = array('GET', 'HEAD', 'OPTIONS');
    public static $UNSAFE = array('POST', 'PUT', 'DELETE', 'PATCH');

    public static $METHOD_VAR = '_method';

    /**
     * identical requests is the same as for a single request.
     */
    public function isIdempotent()
    {
        return in_array(self::current(), self::$IDEMPOTENT);
    }

    public static function fromCommandLine()
    {
        if ($_SERVER['argc'] <= 1) return 'GET';
        $args = array_slice($_SERVER['argv'], 1); // skip file path
        $method = array_shift($args);
        if ( in_array($method, array('GET', 'POST', 'PUT', 'DELETE')) ) {
            return $method;
        }
        return 'GET';
    }

    public static function isCommandLine()
    {
        return PHP_SAPI == 'cli';
    }

    public static function current()
    {
        if (self::isCommandLine()) {
            return self::fromCommandLine();
        }
        $method = @$_SERVER['REQUEST_METHOD'] ?: 'GET';

        if ( $method == 'POST' && isset($_REQUEST[self::$METHOD_VAR]) ) {
            $method = $_REQUEST[self::$METHOD_VAR];
        }

        return strtoupper($method);
    }

    public static function isUnsafe()
    {
        return in_array(self::current(), self::$UNSAFE);
    }
}
