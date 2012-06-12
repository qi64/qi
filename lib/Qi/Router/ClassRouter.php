<?php

namespace Qi\Router;
use Qi\Utils\Inflector,
    Qi\Utils\Arrays;

class ClassRouter
{
    public $tplDir;
    public $routeDefaults = array();
    public $GET = array('/:controller');
    public $POST = array();
    public $PUT = array();
    public $DELETE = array();

    public function __construct($cfg = array())
    {
        foreach($cfg as $k => $v) $this->$k = $v;
    }

    public static function staticRun($cfg = array(), $path = '/', $method = 'GET')
    {
        $router = new self($cfg);
        return $router->run($path);
    }

    public function route($cfg)
    {
        $cfg = array_merge($this->defaults, $cfg);
        $cfg = Arrays::map($cfg, function($v) {
            return Inflector::classify($v);
        });
        extract($cfg);
        $className = "$module\\$controller";
        if ( ! class_exists($className) ) return null;
        $method = "do{$view}";
    }

    public function run($path, $method = 'GET')
    {
        $routes = $this->$method;
        $match = Rx::route($path, $routes, $this->routeDefaults);
        print_r($match);
    }
}
