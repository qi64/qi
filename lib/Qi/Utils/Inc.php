<?php

namespace Qi\Utils;

class Inc
{
    public static $disabled = false;
    protected static $files = array();
    public static $runtimes = array();

    /**
     * Alias this class to global scope
     * @param string $name Global Class Name
     */
    public static function alias($name = 'INC')
    {
        class_alias(__CLASS__, $name);
    }

    public static function begin($file)
    {
        if (static::$disabled) return "";

        static::$files[$file] = microtime(true);
        $nesting = str_repeat(">", count(static::$files));

        $tpl = "\n<!-- %s %s/%s/%s -->\n";
        $name = basename($file);
        $path = dirname($file);
        $dir = basename($path);
        $theme = basename(dirname($path));

        return sprintf($tpl, $nesting, $theme, $dir, $name);
    }

    public static function end($file)
    {
        if (static::$disabled) return "";

        $elapse = @static::$files[$file] ? microtime(true) - static::$files[$file] : 0;
        static::$runtimes[$file] = round($elapse * 1000, 2);
        unset(static::$files[$file]);
        $nesting = str_repeat("<", count(static::$files) + 1);

        $tpl = "\n<!-- %s %s/%s/%s %0.1fms -->\n";
        $name = basename($file);
        $path = dirname($file);
        $dir = basename($path);
        $theme = basename(dirname($path));

        return sprintf($tpl, $nesting, $theme, $dir, $name, $elapse * 1000);
    }

    public static function go($file)
    {
        return isset(static::$files[$file]) ? static::end($file) : static::begin($file);
    }
}

