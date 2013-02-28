<?php

namespace Qi\Controller;

use Qi\Http\Path,
    Qi\Http\Method,
    Qi\Http\Header,
    Qi\Utils\Error,
    Qi\Utils\Html;

class App
{
    public $config = array(
        'view' => array(
            'layoutDefault' => 'main',
            'layoutMask' => "_layouts/%s.php",
            'templateMask' => array("%s/%s.php", "%s.php")
        ),
        'router' => array(
            'defaultNamespace' => 'site',
            'defaultController' => 'home',
            'defaultAction' => 'index'
        )
    );

    public function run()
    {
        $pipe = new Pipe();
        $pipe->queue = $this->createQueue();
        try {
            $pipe->run();
        } catch (\Exception $e) {
            echo $e;
        }
        Header::html();
        echo $pipe->env->output;
    }

    protected function createQueue()
    {
        $queue = array();
        $queue['config'] = $this->stepConfig();
        $queue['path'] = $this->stepPath();
        $queue['method'] = $this->stepMethod();
        $queue['namespace'] = $this->stepNamespace();
        $queue['router'] = $this->stepRouter();
        $queue['classMethod'] = $this->stepClassMethod();
        $queue['controller'] = $this->stepController();
        $queue['view'] = $this->stepView();
        $queue['layout'] = $this->stepLayout();
        $queue['debug'] = $this->stepDebug();
        return $queue;
    }

    protected function stepClassMethod()
    {
        return new Step\ClassMethod();
    }

    protected function stepDebug()
    {
        return new Step\Debug();
    }

    protected function stepConfig()
    {
        $config = $this->config;
        return function($env) use($config) {
            $env->config = $config;
            Header::plain();
        };
    }

    protected function stepPath()
    {
        return function($env) {
            $env->path = Path::current();
        };
    }

    protected function stepMethod()
    {
        return function($env) {
            $env->method = Method::current();
        };
    }

    protected function stepNamespace()
    {
        return function($env) {
            @list($namespace, $path) = explode('/', $env->path, 2);
            if ($namespace == 'admin') {
                $env->namespace = 'admin';
                $env->path = (string)$path;
            }else{
                $env->namespace = $env->config['router']['defaultNamespace'];
            }
        };
    }

    protected function stepRouter()
    {
        return function($env) {
            @list($controller, $action, $env->id) = explode('/', $env->path, 3);
            $env->controller = $controller ?: $env->config['router']['defaultController'];
            $env->action = $action ?: $env->config['router']['defaultAction'];
        };
    }

    protected function stepController()
    {
        return new Step\Controller();
    }

    protected function stepView()
    {
        return function($env) {
            if (@$env->view->template) {
                $viewName = $env->namespace.'/'.$env->view->template;
            }else{
                foreach($env->config['view']['templateMask'] as $mask) {
                    $viewName = sprintf($mask, $env->controller, $env->action);
                    $viewName = $env->namespace.'/'.$viewName;
                    if (stream_resolve_include_path($viewName)) break;
                }
            }
            $env->template = Html::renderFile($viewName, $env->vars);
        };
    }

    protected function stepLayout()
    {
        return function($env) {
            $layoutName = @$env->view->layout ?: $env->config['view']['layoutDefault'];
            $layoutPath = sprintf($env->config['view']['layoutMask'], $layoutName);
            $layoutPath = $env->namespace.'/'.$layoutPath;
            //unset($env->template);
            $env->vars['TPL'] = @$env->template;
            $env->output = Html::renderFile($layoutPath, $env->vars);
        };
    }
}
