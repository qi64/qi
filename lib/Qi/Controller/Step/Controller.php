<?php

namespace Qi\Controller\Step;

class Controller
{
    public function __invoke($env)
    {
        $env->vars = array();

        if ( ! class_exists($env->className) ) {
            return;
            //throw new \DomainException("Controller '$className' não existe!");
        }

        $env->vars = $this->call($env->className, $env->methodName, $env->id);
    }

    protected function call($className, $methodName, $id)
    {
        $controller = new $className;
        $r = new \ReflectionObject($controller);
        if ( ! $r->hasMethod($methodName) ) {
            return null;
            //throw new \BadMethodCallException("$className::$methodName nao existe!");
        }
        $method = $r->getMethod($methodName);
        $params = array();
        foreach($method->getParameters() as $v) {
            $params[$v->name] = $v->isDefaultValueAvailable() ? $v->getDefaultValue() : null; // @TODO throw exception for required parameter
        }
        $params = array_merge($params, $_GET, $_POST, array('id' => $id));

        return $method->invokeArgs($controller, $params);
    }
}
