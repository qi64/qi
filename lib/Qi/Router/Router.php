<?php

namespace Qi\Router;

class Router
{
    public $defaults = array();
    public $GET = array();
    public $POST = array();
    public $PUT = array();
    public $DELETE = array();

    public function run($path, $method = 'GET')
    {
        $routes = $this->$method;
        $match = Rx::route($path, $routes, $this->defaults);
        return $match;
    }
}
