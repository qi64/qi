<?php

namespace Qi\Controller\Step;
use Qi\Utils\Inflector;

class ClassMethod
{
    public $config = array(
        'namespaceMask' => 'App\\Controller\\%s',
        'classMask' => '%sController',
        'methodMask' => "%s_%s" // get_index
    );

    public function __construct($config = array())
    {
        $this->config = array_merge($this->config, $config);
    }

    public function __invoke($env)
    {
        $env->className = $this->getNamespace($env->namespace).'\\'.$this->getClassName($env->controller);
        $env->methodName = $this->getMethodName($env->method, $env->action);
    }

    protected function getNamespace($namespace)
    {
        return sprintf($this->config['namespaceMask'], Inflector::classify($namespace));
    }

    protected function getClassName($controller)
    {
        return sprintf($this->config['classMask'], Inflector::classify($controller));
    }

    protected function getMethodName($method, $action)
    {
        return strtolower(
            sprintf(
                $this->config['methodMask'],
                Inflector::classify($method),
                Inflector::classify($action))
        );
    }
}
