<?php

namespace Qi\Controller\Step;
use Qi\Utils\Inflector;

class ClassMethod
{
    public $config = array(
        'classMethod.namespaceMask' => 'App\\Controller\\%s\\',
        'classMethod.classMask' => '%sController',
        'classMethod.methodMask' => "%s_%s" // get_index
    );

    public function __construct($config = array())
    {
        $this->config = array_merge($this->config, array_intersect_key($config, $this->config));
    }

    public function __invoke($env)
    {
        $env->className = $this->getNamespace(@$env->namespace).$this->getClassName($env->controller);
        $env->methodName = $this->getMethodName($env->method, $env->action);
    }

    protected function getNamespace($namespace)
    {
        return sprintf($this->config['classMethod.namespaceMask'], Inflector::classify($namespace));
    }

    protected function getClassName($controller)
    {
        return sprintf($this->config['classMethod.classMask'], Inflector::classify($controller));
    }

    protected function getMethodName($method, $action)
    {
        return strtolower(
            sprintf(
                $this->config['classMethod.methodMask'],
                Inflector::classify($method),
                Inflector::classify($action))
        );
    }
}
