<?php

namespace Qi\Utils;

class IncludePath
{
    public static function asArray()
    {
        return explode(PATH_SEPARATOR, get_include_path());
    }

    public static function set($paths)
    {
        if (is_array($paths)) $paths = implode(PATH_SEPARATOR, $paths);
        set_include_path($paths);
    }

    public static function append($path)
    {
        set_include_path(get_include_path().PATH_SEPARATOR.$path);
    }

    public static function prepend($path)
    {
        set_include_path($path.PATH_SEPARATOR.get_include_path());
    }	
}
