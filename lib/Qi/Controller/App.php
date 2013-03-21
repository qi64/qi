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
        'view.layoutDefault' => 'main',
        'view.layoutMask' => "_layouts/%s.php",
        'view.templateMask' => array("%s/%s.php", "%s.php"),
        'router.defaultNamespace' => 'site',
        'router.defaultController' => 'home',
        'router.defaultAction' => 'index',
        'classMethod.namespaceMask' => 'App\\Controller\\%s',
        'classMethod.classMask' => '%sController',
        'classMethod.methodMask' => "%s_%s" // get_index
    );

    public $queue = array();

    public function __construct($config = array())
    {
        $this->config = array_merge($this->config, array_intersect_key($config, $this->config));
        $this->queue = $this->createQueue();
    }

    public function run($pipe = null)
    {
        if ( ! $pipe ) $pipe = new Pipe();
        $pipe->queue = $this->queue;
        try {
            $pipe->run();
        } catch (\Exception $e) {
            echo $e;
        }
        Header::html();
        echo $pipe->env->output;
        return $pipe->env;
    }

    protected function createQueue()
    {
        $queue = array();
        $queue['config'] = $this->stepConfig();
        $queue['path'] = $this->stepPath();
        $queue['phpinfo'] = $this->stepPhpInfo();
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

    protected function stepPhpInfo()
    {
        return new Step\PhpInfo();
    }

    protected function stepClassMethod()
    {
        return new Step\ClassMethod($this->config);
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
            //Header::plain();
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
            $env->controller = $controller ?: $env->config['router.defaultController'];
            $env->action = $action ?: $env->config['router.defaultAction'];
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
                $viewName = (@$env->namespace ? $env->namespace.'/' : '').$env->view->template;
            }else{
                foreach($env->config['view.templateMask'] as $mask) {
                    $viewName = sprintf($mask, $env->controller, $env->action);
                    $viewName = (@$env->namespace ? $env->namespace.'/' : '').$viewName;
                    if (stream_resolve_include_path($viewName)) break;
                }
            }

            $env->template = Html::renderFile($viewName, $env->vars);
        };
    }

    protected function stepLayout()
    {
        return function($env) {
            $layoutName = @$env->view->layout ?: $env->config['view.layoutDefault'];
            $layoutPath = sprintf($env->config['view.layoutMask'], $layoutName);
            $layoutPath = (@$env->namespace ? $env->namespace.'/' : '').$layoutPath;
            //unset($env->template);
            $env->vars['TPL'] = @$env->template;

            $env->output = Html::renderFile($layoutPath, $env->vars);
        };
    }
}
