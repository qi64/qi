<?php

namespace Qi\Controller;
use ArrayIterator;

class Pipe extends ArrayIterator
{
    public $env;
    public $runtimes = array();

    public function __construct()
    {
        $this->env = new \stdClass();
        $this->env->runtimes = &$this->runtimes;
    }

    public function run()
    {
        // @TODO better start definition
        $before = $_SERVER['REQUEST_TIME_FLOAT'];

        $this->measure($before);

        foreach($this as $name => $closure) {
            error_log($name);
            $before = microtime(true);
            call_user_func($closure, $this->env);
            $this->measure($before, $name);
        }
    }

    protected function measure($before, $name = 'start')
    {
        $this->runtimes[$name] = $this->elapse($before);
    }

    protected function elapse($before)
    {
        return round(microtime(true) - $before, 5) * 1000;
    }
}
