<?php

namespace Qi\Http;
use ArrayIterator, IteratorAggregate, ArrayAccess;

/**
 * @TODO don't save on session when empty
 * Class Flash
 * @package Qi\Http
 */
class Flash implements IteratorAggregate, ArrayAccess
{
    protected static $singleton;
    protected $current = array();
    protected $next = array();

    public function __construct()
    {
        if (!isset($_SESSION)) session_start();
        $k = 'env.flash';
        if ( ! isset($_SESSION[$k]) ) {
            $_SESSION[$k] = array();
        }
        // always rotate flash messages, even when not used
        $this->current = $_SESSION[$k];
        $_SESSION[$k] = array();
        $this->next = &$_SESSION[$k];
    }

    public function getIterator()
    {
        return new ArrayIterator($this->current);
    }

    public static function singleton()
    {
        if (! self::$singleton) self::$singleton = new self;
        return self::$singleton;
    }

    public static function error($msg)
    {
        self::__callStatic(__FUNCTION__, $msg);
    }

    public static function warning($msg)
    {
        self::__callStatic(__FUNCTION__, $msg);
    }

    public static function notice($msg)
    {
        self::__callStatic(__FUNCTION__, $msg);
    }

    public static function alert($msg)
    {
        self::__callStatic(__FUNCTION__, $msg);
    }

    public static function ok($msg)
    {
        self::success($msg);
    }

    public static function success($msg)
    {
        self::__callStatic(__FUNCTION__, $msg);
    }

    public static function __callStatic($label, $msg)
    {
        $msg = (array)$msg; // accept message as string too
        $flash = self::singleton();
        $flash[$label] = reset($msg);
    }

    public function __set($name, $var)
    {
        $this->__call($name, array($var));
    }

    public function __get($method)
    {
        if ($method == 'ok') $method = 'success';
        return $this->offsetGet($method);
    }

    public function __call($method, $args)
    {
        if ($method == 'ok') $method = 'success';
        $this->offsetSet($method, reset($args));
    }

    public function offsetExists($offset)
    {
        return isset($this->next[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->next[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->next[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->next[$offset]);
    }
}
