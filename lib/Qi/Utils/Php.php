<?php

namespace Qi\Utils;

use RuntimeException;

class Php
{
    /**
     * @var \Closure $fatal_error_handler
     */
    public static $fatal_error_handler = array(__CLASS__, 'fatal_error_handler');

    public static function fileStack($limit = 0)
    {
        $backtrace = debug_backtrace(
            defined("DEBUG_BACKTRACE_IGNORE_ARGS")
                ? DEBUG_BACKTRACE_IGNORE_ARGS
                : false, $limit);
        return array_map(function($item) {
            return $item['file'];
        }, $backtrace);
    }

    public static function data2path($data)
    {
        return "data://text/plain;base64,".base64_encode($data);
    }

    public static function isLocal()
    {
        if (PHP_SAPI == 'cli' || PHP_SAPI == 'cli-server') return true;
        return isset($_SERVER["REMOTE_ADDR"]) && ($_SERVER["REMOTE_ADDR"] == $_SERVER["SERVER_ADDR"]);
    }

    public static function tmpDir()
    {
        $options = array();
        $options[] = sys_get_temp_dir();
        $options[] = ini_get('upload_tmp_dir');
        $options[] = ini_get('session.save_path');
        foreach($options as $dir) {
            // error with open_basedir
            if (@is_writable($dir)) return $dir;
        }
        throw new RuntimeException("no writeable tmp_dir");
    }

    public static function curl_get_contents($url)
    {
        if (ini_get('allow_url_fopen')) return file_get_contents($url);
        if ( ! extension_loaded('curl') ) {
            throw new RuntimeException('no remote access, allow_url_fopen=off and not curl.');
        }
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        // E_WARNING: curl_setopt(): CURLOPT_FOLLOWLOCATION cannot be activated when in safe_mode or an open_basedir is set
        //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, (int)ini_get('default_socket_timeout'));

        $data = curl_exec($curl);

        curl_close($curl);

        return $data;
    }

    public static function ob_start()
    {
        ob_start( array(__CLASS__, 'ob_start_callback') );
    }

    public static function ob_start_callback($output)
    {
        $error = error_get_last();
        if ( @$error['type'] == E_ERROR ) {
            preg_match("!Call Stack:\n(.+)$!sim", $output, $matches);
            $error['backtrace'] = rtrim(end($matches));
            $callback = self::$fatal_error_handler;
            return $callback($error);
        } else {
            return $output;
        }
    }

    public static function fatal_error_handler($error)
    {
        @header("Content-Type: text/plain");
        return sprintf(
            "%s\n%s:%s\n%s",
            $error['message'],
            $error['file'],
            $error['line'],
            $error['backtrace']
        );
    }

    /**
     * Check if a function is php defined (interal)
     * @param $f string function name
     * @return bool
     */
    public static function isInternalFunction($f)
    {
        if ( ! function_exists($f) ) return false;
        $functions = get_defined_functions();
        return isset($functions['internal'][$f]);
    }

    /**
     * Check if a function is user defined
     * @param $f string function name
     * @return bool
     */
    public static function isUserFunction($f)
    {
        if ( ! function_exists($f) ) return false;
        $functions = get_defined_functions();
        return isset($functions['user'][$f]);
    }
}
