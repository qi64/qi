<?php

namespace Qi\Controller;

class Pipe
{
    public $runtimes = array();
    public $env;
    public $queue = array();

    public function __construct()
    {
        $this->env = new \stdClass();
    }

    public function run()
    {
        $this->runtimes['start'] = round(microtime(true) - APP_START, 4) * 1000;

        foreach($this->queue as $name => $closure) {
            $before = microtime(true);
            $closure($this->env);
            $this->runtimes[$name] = round(microtime(true) - $before, 4) * 1000;
        }
    }

    protected function validateTraversable($var)
    {
        if (is_array($var) || $var instanceof \Traversable) return;
        throw new \DomainException("array or Traversable required, got $var");
    }
}