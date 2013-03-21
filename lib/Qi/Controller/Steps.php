<?php

namespace Qi\Controller;

use Qi\Http\Flash,
    Qi\Http\Header,
    Qi\Utils\Error,
    Qi\Utils\Php,
    ReflectionMethod,
    ReflectionObject,
    ReflectionFunction;

/**
 * @TODO migrate path/method to a Request object
 * @TODO migrate Steps to php5.4 traits
 * @TODO alias to redirect an url to another
 * @TODO custom router
 * @TODO password protect
 * @TODO php/debug,info
 * @TODO domain/subdomain
 * @TODO path thumb generator
 * @TODO automatically store uploaded files/images and assign path in request.
 * @TODO handle /produtos/23 and /produtos/23/edit,view
 * @TODO router to functions
 * @TODO cache html on system files
 * @TODO default type ???
 * @TODO multi template engine support
 * @TODO JSONP https://github.com/rack/rack-contrib/blob/master/lib/rack/contrib/jsonp.rb
 * @TODO HTTP Cache https://github.com/rtomayko/rack-cache
 * @TODO Redirect static fallback to production site: https://github.com/dolzenko/rack-static_fallback/blob/master/lib/rack/static_fallback.rb
 * @TODO something for /mailer/contato /mailer/newsletter
 * Class Steps
 * @package Qi\Controller
 */
class Steps
{
    // @TODO migrate cfg to class for auto_complete support
    public static $cfg_defaults = array(
        'method_param_name' => '_method',
        'default_controller_name' => 'default',
        'default_action_name' => 'index',
        'fallback_action_name' => 'default',
        'controller_class' => 'App\\Controller\\%sController',
        'tpl_file' => '%s.php',
        'layout_name' => 'default',
        'layout_file' => 'layout/%s.php',
        'render_disable_error' => E_NOTICE,

        'auth' => array(
            'path_login'  => 'login',
            'path_logout' => 'logout',
            'restrict'    => '.*',
            'msg_login'   => 'Usuário logado com Sucesso!',
            'msg_logout'  => 'Usuário deslogado com Sucesso!',
            'msg_error'   => 'Usuário ou Senha Inválidos!',
            'msg_denied'  => 'Acesso Negado!',
        ),

        'csrf' => array(
            'field'  => '_csrf',
            'header' => 'HTTP_X_CSRF_TOKEN',
            'format' => '%s %s %s %s',
            'secret' => '-_=+ldnt49348dfjy5l08^.,/?!m',
            'fail'   => 'CSRF FAIL!'
        )
    );

    public $cfg = array();

    public function __construct($cfg = array())
    {
        $this->cfg = array_merge(self::$cfg_defaults, $cfg);
    }

    protected function pipe($pipe, $method)
    {
        $pipe[$method] = array($this, $method);
    }

    public function group_all($pipe)
    {
        $this->pipe($pipe, 'cfg');

        $this->pipe($pipe, 'flash');

        $this->group_path($pipe);
        $this->group_method($pipe);

        $this->pipe($pipe, 'csrf');

        $this->pipe($pipe, 'phpinfo');

        $this->pipe($pipe, 'auth_closure');
        $this->pipe($pipe, 'auth_login');
        $this->pipe($pipe, 'auth_logout');

        $this->group_route($pipe);

        $this->group_controller($pipe);
        $this->group_closure($pipe);

        $this->group_view($pipe);

        $this->pipe($pipe, 'debugbar');
    }

    public function group_path($pipe)
    {
        $this->pipe($pipe, 'path_url');
        $this->pipe($pipe, 'path_format');
        $this->pipe($pipe, 'path_type');
        $this->pipe($pipe, 'path_remove_base');
    }

    public function group_method($pipe)
    {
        $this->pipe($pipe, 'method');
        $this->pipe($pipe, 'method_from_param');
        $this->pipe($pipe, 'method_format');
    }

    public function group_route($pipe)
    {
        $this->pipe($pipe, 'route');
        $this->pipe($pipe, 'route_defaults');
        $this->pipe($pipe, 'route_params');
    }

    public function group_controller($pipe)
    {
        $this->pipe($pipe, 'controller_class');
        $this->pipe($pipe, 'controller_default');
        $this->pipe($pipe, 'controller_method');
        $this->pipe($pipe, 'controller_instance');
        $this->pipe($pipe, 'controller_method_default');
    }

    public function group_closure($pipe)
    {
        $this->pipe($pipe, 'closure_create');
        $this->pipe($pipe, 'closure_params');
        $this->pipe($pipe, 'closure_run');
    }

    public function group_view($pipe)
    {
        $this->pipe($pipe, 'tpl_name');
        $this->pipe($pipe, 'tpl_file');

        $this->pipe($pipe, 'layout_name');
        $this->pipe($pipe, 'layout_file');

        $this->pipe($pipe, 'tpl_include');
    }

    public function cfg($env)
    {
        $env->cfg = $this->cfg;
    }

    public function flash($env)
    {
        $env->flash = Flash::singleton();
    }

    public function path_url($env)
    {
        $env->path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if ($env->path == $_SERVER['SCRIPT_NAME']) $env->path = '';
    }

    public function path_format($env)
    {
        $path = @$env->path;

        $path = trim($path, ' /\\');
        $path = preg_replace('!/+!', '/', $path); // collapse double slashs
        $path = urldecode($path);
        $path = strtolower($path);

        $env->path = $path;
    }

    public function path_type($env)
    {
        $path = @$env->path;

        if ( ! strpos($path, '.') ) return;
        preg_match('!(.+)\.([^\./]{1,4})$!', $path, $matches);
        @list($env->path_full, $env->path, $env->type) = $matches;
    }

    public function path_remove_base($env)
    {
        $path = @$env->path;

        if ( ! $path ) return;
        $base = pathinfo($_SERVER['SCRIPT_NAME'], PATHINFO_DIRNAME);
        if ( ! $base ) return;
        $base = trim($base, '/');
        $dir = preg_quote($base);
        $path = preg_replace("!^$dir/?!", '', $path, 1);

        $env->path_base = $base;
        $env->path = $path;
    }

    public function method($env)
    {
        $env->method = @$_SERVER['REQUEST_METHOD'] ?: 'GET';
    }

    public function method_from_param($env)
    {
        $env->method = @$_POST[$env->cfg['method_param_name']] ?: @$env->method;
    }

    public function method_format($env)
    {
        $env->method = trim(strtoupper(@$env->method));
    }

    /**
     * https://github.com/envygeeks/rack-csrf
     * https://github.com/baldowl/rack_csrf/blob/master/lib/rack/csrf.rb
     * https://github.com/BKcore/NoCSRF/blob/master/nocsrf.php
     * https://github.com/deceze/Kunststube-CSRFP
     * @param $env
     */
    public function csrf($env)
    {
        extract($env->cfg['csrf'], EXTR_SKIP);

        $form_token = @$_POST[$field] ?: @$_SERVER[$header];
        $token = sprintf($format,
                          $secret,
                          $_SERVER['HTTP_USER_AGENT'],
                          $_SERVER['REMOTE_ADDR'],
                          session_id()
                  );
        $token = md5($token);
        $env->csrf_token = $token;

        if ( @$env->method != 'GET' && $form_token != $token ) die($fail);
    }

    public function auth_closure($env)
    {
        $env->auth_closure = function($user, $passwd) {
            return $user == 'admin@local';
        };
    }

    public function auth_login($env)
    {
        // @TODO separate admin from user
        extract($env->cfg['auth'], EXTR_SKIP);

        $path = $env->path;
        $env->user = @$_SESSION['user'];

        if ( preg_match("!^$path_login$!", $path) ) {
            $env->layout_name = 'login';
            if ($env->method == 'GET') {
                return;
            }else{
                $auth_closure = $env->auth_closure;
                $env->user = $auth_closure(@$_POST['user'], @$_POST['passwd']);
                if ( $env->user ) {
                    // @TODO redirect to forward
                    $_SESSION['user'] = $env->user;
                    $this->redirect($env, "", array('success' => $msg_login));
                }else{
                    $this->redirect($env, $path_login, array('error' => $msg_error));
                }
            }
        }
        if ( preg_match("!^$restrict$!", $path) && isset($env->user) ) return;

        $this->redirect($env, $path_login, array('warning' => $msg_denied));
    }

    public function auth_logout($env)
    {
        extract($env->cfg['auth'], EXTR_SKIP);

        $path = $env->path;

        if ( preg_match("!^$path_logout$!", $path) ) {
            unset($_SESSION['user']);
            $this->redirect($env, $path_login, array('info' => $msg_logout));
        }
    }

    protected function redirect($env, $url, $flash = array())
    {
        if ( @$env->path_base ) {
            $url = "$env->path_base/$url";
        }
        $url = "/$url";
        foreach($flash as $level => $message) {
            $env->flash[$level] = $message;
        }
        Header::location($url);
    }

    public function phpinfo($env)
    {
        $pattern = 'phpinfo/?(.*)';
        $path = @$env->path;
        // @TODO make it possible to disable
        // @TODO password protect
        if ( preg_match("!^$pattern$!", $path, $matches) ) {
            $const = @$matches[1] ?: 'all';
            $const = constant( strtoupper("info_$const") );
            phpinfo($const);
            exit;
        }
    }

    public function route($env)
    {
        @list($env->controller_name, $env->action_name, $env->id) = explode('/', @$env->path, 3);
    }

    public function route_defaults($env)
    {
        $env->controller_name = $env->controller_name ?: $env->cfg['default_controller_name'];
        $env->action_name     = $env->action_name     ?: $env->cfg['default_action_name'];
    }

    public function route_params($env)
    {
        $id = @$env->id;

        $env->route_params = array();

        if (! $count = substr_count($env->id, '/')) return;
        $parts = explode('/', $id);
        if ($count % 2 == 0) {
            $id = array_shift($parts);
        }
        $chunks = array_chunk($parts, 2);
        $params = array();
        foreach($chunks as $chunk) {
            list($k, $v) = $chunk;
            $params[$k] = $v;
        }

        $env->route_params = $params;
        $env->id = $id;
    }

    public function controller_class($env)
    {
        $controller_name = @$env->controller_name;

        $controller_name = strtr($controller_name, '_-.', '   ');
        $controller_name = ucwords($controller_name);
        $controller_name = str_replace(' ', '', $controller_name);
        $controller_class = sprintf($env->cfg['controller_class'], $controller_name);

        $env->controller_class = $controller_class;
    }

    public function controller_default($env)
    {
        if ( ! class_exists($env->controller_class ) ) {
            $env->controller_class  = sprintf($env->cfg['controller_class'],
                ucwords($env->cfg['default_controller_name']));
        }
    }

    public function controller_method($env)
    {
        $action = @$env->action_name;
        $action = strtr($action, '-.', '__');
        $env->controller_method = sprintf("%s_%s", $env->method, $action);
    }

    public function controller_instance($env)
    {
        $controller_class = @$env->controller_class;
        if ( ! class_exists($controller_class) ) return ;
        $env->controller_instance = new $controller_class($env);
    }

    public function controller_method_default($env)
    {
        if ( ! method_exists($env->controller_instance, $env->controller_method) ) {
            $env->controller_method = sprintf("%s_%s",
                                              $env->method,
                                              $env->cfg['fallback_action_name']);
        }
    }

    public function closure_create($env)
    {
        if ( ! isset($env->controller_instance) ) return ;

        $controller_method = @$env->controller_method;
        $controller = $env->controller_instance;

        if ( ! method_exists($controller, $controller_method) ) return;

        // convert object->method to Closure
        $r = new ReflectionObject($controller);
        $method = $r->getMethod($controller_method);
        $closure = $method->getClosure($controller);

        $env->controller_closure = $closure;
    }

    public function closure_params($env)
    {
        $env->closure_params = array_merge($_GET,
                                           $_POST,
                                           (array)@$env->route_params,
                                           array('id' => @$env->id)
        );
    }

    public function closure_run($env)
    {
        $closure = @$env->controller_closure;
        $closure_params = @$env->closure_params;
        $controller_class = @$env->controller_class;
        $controller_instance = @$env->controller_instance;

        if ( ! is_callable($closure) && is_object($closure) ) return;

        $r = isset($env->controller_instance) ? new ReflectionMethod($controller_class, $env->controller_method)
                                              : new ReflectionFunction($closure);
        $params = array();
        foreach($r->getParameters() as $v) {
            // @TODO throw exception for required parameter no present on $closure_params
            $params[$v->name] = $v->isDefaultValueAvailable() ? $v->getDefaultValue() : null;
        }
        $params = array_merge($params, $closure_params);

        $env->view_params = isset($env->controller_instance) ? $r->invokeArgs($controller_instance, $params)
                                                             : $r->invokeArgs($params);
    }

    public function tpl_name($env)
    {
        if ( isset($env->tpl_name) ) return;
        $parts = array(@$env->controller_name);
        if ( $env->action_name && $env->action_name != $env->cfg['default_action_name'] )
            $parts[] = @$env->action_name;

        $env->tpl_name = implode('/', $parts);
    }

    public function tpl_file($env)
    {
        if ( isset($env->tpl_file) ) return;
        $env->tpl_file = sprintf($env->cfg['tpl_file'], $env->tpl_name);
    }

    public function layout_name($env)
    {
        if ( isset($env->layout_name) ) return;
        $env->layout_name = $env->cfg['layout_name'];
    }

    public function layout_file($env)
    {
        if ( isset($env->layout_file) ) return;
        $env->layout_file = sprintf($env->cfg['layout_file'], $env->layout_name);
    }

    public function tpl_include($env)
    {
        // @TODO handle missing templates/layouts
        $env->tpl_output = $this->render($env->tpl_file,    @$env->view_params, null, $env);
        $env->tpl_output = $this->render($env->layout_file, @$env->view_params, $env->tpl_output, $env);
    }

    protected function render($__FILE__, $__VARS__ = array(), $TPL = null, $env)
    {
        if ( ! stream_resolve_include_path($__FILE__) ) return $TPL;
        Php::ob_start();
        Error::disable( $env->cfg['render_disable_error'] );
        extract((array)$__VARS__, EXTR_SKIP);
        include $__FILE__;
        Error::pop();
        return ob_get_clean();
    }

    public function debugbar($env)
    {
        // @TODO enable/disable/configure
        $out = $env->tpl_output;
        unset($env->tpl_output);
        $runtimes = $env->runtimes;
        unset($env->runtimes);

        Php::ob_start();
        include 'layout/debugbar.php';
        $debugbar = ob_get_clean();
        $out = str_replace('</body>', "$debugbar</body>", $out);

        $env->tpl_output = $out;
    }
}
