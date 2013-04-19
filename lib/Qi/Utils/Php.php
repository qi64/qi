<?php

namespace Qi\Utils;

use RuntimeException;

class Php
{
    /**
     * Default fatal error
     * @var \Closure $fatal_error_handler
     */
    public static $fatal_error_handler = array(__CLASS__, 'fatal_error_handler');

    /**
     * @TODO não esta pronta ainda
     */
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

    /**
     * Permite que uma string seja utilizada em uma função de file, exemplo:
     * echo file_get_contents(Php::data2path("teste")) == "teste";
     */
    public static function data2path($data)
    {
        return "data://text/plain;base64,".base64_encode($data);
    }

    /**
     * Verifica se o servidor esta rodando local
     * @TODO melhorar implementação
     */
    public static function isLocal()
    {
        if (PHP_SAPI == 'cli' || PHP_SAPI == 'cli-server') return true;
        return isset($_SERVER["REMOTE_ADDR"]) && ($_SERVER["REMOTE_ADDR"] == $_SERVER["SERVER_ADDR"]);
    }

    /**
     * retorna um diretório temporário que permita escrita e esteja dentro do open_basedir
     * @return string
     */
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

    /**
     * Substituir file_get_contents para endereços remotos,
     * utilizando curl quando allow_url_fopen for false.
     */
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

    /**
     * Permite capturar erro fatal, ÚNICA MANEIRA!
     * Faz o mesmo que ob_start, mas permite exibir apenas o erro fatal e logar caso seja necessário.
     */
    public static function ob_start($fatal_error_handler = null)
    {
        $fatal_error_handler = $fatal_error_handler ?: static::$fatal_error_handler;
        ob_start( function($output) use ($fatal_error_handler) {
            // se o erro for fatal, não adianta fazer nada, debug_backtrace ou exception,
            // só resta logar e retornar a string contendo o erro fatal. 
            $error = error_get_last();
            return @$error['type'] == E_ERROR
                ? call_user_func($fatal_error_handler, $error)
                : $output;
        });
    }

    /**
     * Handler padrão para tratamento de erro fatal,
     * @param array $error array retornado por error_get_last()
     * @return string para ser exibida na tela.
     * @TODO implementar LOG
     */
    public static function fatal_error_handler($error)
    {
        @header("Content-Type: text/plain");
        extract($error);
        return "\n$message\n$file:$line\n";
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
