<?php

namespace Qi\Controller;

use Qi\Utils\Error;
use Qi\Utils\Php;

class App
{
    /**
     * @var $pipe Pipe
     */
    public $pipe;
    /**
     * @var $steps Steps
     */
    public $steps;

    public function setup()
    {
        $this->pipe = new Pipe();
        $this->steps = new Steps();
        Error::toException();
        //Php::ob_start();
        $this->steps->group_all($this->pipe);
    }

    public function run()
    {
        ($this->pipe && $this->steps) || $this->setup();
        $this->pipe->run();
        return $this->pipe->env->tpl_output;
    }
}
