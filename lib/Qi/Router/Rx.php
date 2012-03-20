<?php

namespace Qi\Router;

class Rx
{
    // translate url patterns to regular expressions
    public static $COMPILE_RULES = array(
        '!\)!' => ')?', // optional
        '!\.:format$!' => '.(?P<format>\w+)', // auto format
        '!\.!' => '\.', // must came after dot patterns above
        '!\*(\w+)!' => '(?P<$1>.+?)',
        '!\*!' => '(?P<slug>.+?)',
        '!m:(\w+)!' => '(?P<module>$1)',
        '!c:(\w+)!' => '(?P<controller>$1)',
        '!v:(\w+)!' => '(?P<view>$1)',
        '!:(\w+)!' => '(?P<$1>[^/]+?)',
        '!^(\w+)!' => '(?P<controller>$1)',
        '!^(.+)/(\w+)!' => '$1/(?P<view>$2)',
    );

    public static $DEFAULT_MATCH = array(
        'module' => null,
        'controller' => null,
        'view' => null,
        'id' => null,
        'format' => null
    );

    public static function compile($route)
    {
        $route = self::format_path($route);
        $pattern = preg_replace(array_keys(self::$COMPILE_RULES), self::$COMPILE_RULES, $route);
        return "!^$pattern$!";
    }

    public static function route($path, $routes, $default = array())
    {
        foreach($routes as $route => $handler) {
            if (is_numeric($route)) $route = $handler;
            $match = self::match($path, $route, $default);
            if ($match !== null) return array($match, $handler, $route, $default);
        }
        return null;
    }

    public static function match($path, $route, $default = array())
    {
        $path = self::format_path($path);
        $pattern = self::compile($route);
        if ( ! preg_match($pattern, $path, $matches) ) {
            return null;
        }
        foreach($matches as $k => $v) {
            if (is_numeric($k)) {
                unset($matches[$k]);
            }
        }
        $matches = array_merge(self::$DEFAULT_MATCH, $default, $matches);
        return $matches;
    }

    public static function format_path($path)
    {
        return trim($path, '/'); // ignore slashes at begin/end.
    }
}
