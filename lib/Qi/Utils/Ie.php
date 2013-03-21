<?php

namespace Qi\Utils;

/**
 * Internet Explorer Helpers
 * Class Ie
 * @package Qi\Utils
 */
class Ie {
    public static function is($v)
    {
        return "<!--[if IE $v]>";
    }

    public static function gt($v)
    {
        self::open(__METHOD__, $v);
    }

    public static function lt($v)
    {
        self::open(__METHOD__, $v);
    }

    public static function gte($v)
    {
        self::open(__METHOD__, $v);
    }

    public static function lte($v)
    {
        self::open(__METHOD__, $v);
    }

    public static function end()
    {
        return '<![endif]-->';
    }

    public static function not()
    {
        return '<!--[if !IE]> -->';
    }

    public static function endnot()
    {
        return '<!-- <![endif]-->';
    }

    protected static function open($name, $v)
    {
        return "<!--[if $name IE $v]>";
    }
}
