<?php

namespace Qi\Utils;

class Php
{
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
        throw new \RuntimeException("no writeable tmp_dir");
    }

    public static function curl_get_contents($url)
    {
        if (ini_get('allow_url_fopen')) return file_get_contents($url);

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
}
