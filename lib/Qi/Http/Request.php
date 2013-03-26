<?php

namespace Qi\Http;

/**
 * Class Request
 * @package Qi\Http
 * https://github.com/symfony/HttpFoundation
 * https://github.com/illuminate/http
 * http://laravel.com/api/source-class-Laravel.URL.html#3
 * http://laravel.com/api/source-class-Laravel.Request.html#3
 * http://laravel.com/api/class-Laravel.Response.html#3
 * http://laravel.com/api/class-Laravel.URI.html
 */
class Request
{
    public $uri;
    public $path;
    public $format;
    public $base;
    public $method;

    public function __construct($server = array())
    {
        $s = array_merge($_SERVER, $server);
        $this->uri = @$s['REQUEST_URI'];
        $path = parse_url($this->uri, PHP_URL_PATH);
        if ($path == $s['SCRIPT_NAME']) $path = ''; // /admin/index.php is the same as ''

        $path = trim($path, ' /\\');
        $path = preg_replace('!/+!', '/', $path); // collapse double slashs
        $path = urldecode($path);
        $path = strtolower($path);

        if ( strpos($path, '.') ) { // extract format from path
            preg_match('!(.+)\.([a-z0-9]{1,5})$!', $path, $matches);
            @list($oldpath, $path, $this->format) = $matches;
        }

        $base = pathinfo($s['SCRIPT_NAME'], PATHINFO_DIRNAME);
        if ( $base ) {
            $base = trim($base, '/');
            $dir = preg_quote($base);
            $path = preg_replace("!^$dir/?!", '', $path, 1);
        }

        $method = @$s['REQUEST_METHOD'] ?: 'GET';
        if ($method == 'POST') $method = @$_POST['_method'] ?: $method;
        $method = trim(strtoupper($method));

        $this->path = $path;
        $this->base = $base;
        $this->method = $method;
    }

    /**
     * return the client ip address based on many $_SERVER keys options
     * @return string
     */
    public function ip()
    {
        $keys = "HTTP_VIA HTTP_CLIENT_IP HTTP_X_FORWARDED_FOR HTTP_X_FORWARDED HTTP_X_CLUSTER_CLIENT_IP HTTP_FORWARDED_FOR HTTP_FORWARDED REMOTE_ADDR";
        foreach(explode(" ", $keys) as $key) {
            if ( ! isset($_SERVER[$key]) ) continue;
            $ip = $_SERVER[$key];
            $ip = explode(",", $ip);
            return trim(reset($ip)); // assume the first one is the client ip address
        }
    }

    public function clientHost()
    {
        return gethostbyaddr($this->ip());
    }
}
