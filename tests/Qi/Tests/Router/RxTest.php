<?php

namespace Qi\Tests\Router;
use Qi\Router\Rx;

class RxTest extends \PHPUnit_Framework_TestCase
{
    public function testEmpty()
    {
        $this->assertEquals('!^$!', Rx::compile('/'));
        $this->assertEquals(Rx::$DEFAULT_MATCH, Rx::match('/', '/'));
    }

    public function testRoot()
    {
        $this->assertEquals('!^(?P<controller>root)$!', Rx::compile('/root/'));
        $e = Rx::$DEFAULT_MATCH;
        $e['controller'] = 'root';
        $this->assertEquals($e, Rx::match('root', '/root/'));
    }

    public function testMatchModule()
    {
        $r = Rx::match('/admin/resource/new', '/m:admin/c:resource/v:new');
        $e = Rx::$DEFAULT_MATCH;
        $e['module'] = 'admin';
        $e['controller'] = 'resource';
        $e['view'] = 'new';
        $this->assertEquals($e, $r);
    }

    public function testMatchControllerView()
    {
        $e = Rx::$DEFAULT_MATCH;
        $e['controller'] = 'resource';
        $e['view'] = 'edit';
        $e['resource_id'] = '23';
        $r = Rx::match('/resource/edit/23', '/resource/edit/:resource_id');
        $this->assertEquals($e, $r);
        $r = Rx::match('/resource/23/edit', '/resource/:resource_id/edit');
        $this->assertEquals($e, $r);
    }

    public function testControllerView()
    {
        $this->assertEquals('!^(?P<controller>resource)/(?P<view>new)$!', Rx::compile('/resource/new'));
        $e = Rx::$DEFAULT_MATCH;
        $e['controller'] = 'resource';
        $e['view'] = 'new';
        $this->assertEquals($e, Rx::match('resource/new', '/resource/new/'));
    }

    public function testControllerViewId()
    {
        $route = '/resource/edit/:id';
        //$this->assertEquals('!^(?P<controller>resource)/(?P<view>edit)/(?P<id>\w+)$!', Rx::compile($route));
        $e = Rx::$DEFAULT_MATCH;
        $e['controller'] = 'resource';
        $e['view'] = 'edit';
        $e['id'] = '23';
        $this->assertEquals($e, Rx::match('resource/edit/23', $route));
    }
    
    public function testIdWithDot()
    {
        $route = '/resource/edit/:id';
        $e = Rx::$DEFAULT_MATCH;
        $e['controller'] = 'resource';
        $e['view'] = 'edit';
        $e['id'] = 'teste.com.br';
        $this->assertEquals($e, Rx::match('resource/edit/teste.com.br', $route));
    }

    public function testAction()
    {
        $route = '/:action';
        //$this->assertEquals('!^(?P<action>\w+)$!', Rx::compile($route));
        $e = Rx::$DEFAULT_MATCH;
        $e['action'] = 'update';
        $this->assertEquals($e, Rx::match('/update', $route));
    }

    public function testSlug()
    {
        $route = '/*';
        //$this->assertEquals('!^(?P<slug>.+)$!', Rx::compile($route));
        $e = Rx::$DEFAULT_MATCH;
        $e['slug'] = 'cms/article/how-to-do';
        $this->assertEquals($e, Rx::match('/cms/article/how-to-do/', $route));
    }

    public function testControllerViewSlugOptional()
    {
        $route = '/cms/show(/*)';
        //$this->assertEquals('!^(?P<controller>cms)/(?P<view>show)(/(?P<slug>.+))?$!', Rx::compile($route));
        $e = Rx::$DEFAULT_MATCH;
        $e['controller'] = 'cms';
        $e['view'] = 'show';
        $this->assertEquals($e, Rx::match('/cms/show/', $route));
        $e['slug'] = 'empresa/sobre';
        $this->assertEquals($e, Rx::match('/cms/show/empresa/sobre', $route));
    }

    public function testOptional()
    {
        $route = '/posts(/:year(/:month(/:day)))';
        //$this->assertEquals('!^(?P<controller>posts)(/(?P<year>\w+)(/(?P<month>\w+)(/(?P<day>\w+))?)?)?$!', Rx::compile($route));
        $e = Rx::$DEFAULT_MATCH;
        $e['controller'] = 'posts';
        $this->assertEquals($e, Rx::match('/posts', $route));
        $e['year'] = '2012';
        $this->assertEquals($e, Rx::match('/posts/2012', $route));
        $e['month'] = '03';
        $this->assertEquals($e, Rx::match('/posts/2012/03', $route));
        $e['day'] = '27';
        $this->assertEquals($e, Rx::match('/posts/2012/03/27', $route));
    }

    public function testAutoFormat()
    {
        $route = '/resources.:format';
        //$this->assertEquals('!^(?P<controller>resources)\.(?P<format>\w+)$!', Rx::compile($route));
        $r = Rx::match('/resources.json', $route);
        $this->assertEquals('json', $r['format']);
    }

    public function testAutoFormatOptional()
    {
        $route = '/resources(.:format)';
        //$this->assertEquals('!^(?P<controller>resources)(\.(?P<format>\w+))?$!', Rx::compile($route));
        $r = Rx::match('/resources', $route);
        $e = Rx::$DEFAULT_MATCH;
        $e['controller'] = 'resources';
        $this->assertEquals($e, $r);
        $r = Rx::match('/resources.xml', $route);
        $e['format'] = 'xml';
        $this->assertEquals($e, $r);
    }

    public function testNotFound()
    {
        $this->assertNull(Rx::match('/home', '/'));
    }

    public function testCustomMatchDefault()
    {
        $e = Rx::$DEFAULT_MATCH;
        $e['module'] = 'admin';
        $e['controller'] = 'root';
        $this->assertEquals($e, Rx::match('/', '/', array('controller' => 'root', 'module' => 'admin')));
    }

    public function testMany()
    {
        $routes = array();
        $routes['/'] = array(
            '/' => Rx::$DEFAULT_MATCH,
            '/home' => null,
        );
        $routes['/admin/users/new'] = array(
            '/admin/users/new' => array_merge(Rx::$DEFAULT_MATCH, array('controller' => 'admin', 'view' => 'new')),
        );
        $routes['/page/*(.:format)'] = array(
            '/page/home/about' => array_merge(Rx::$DEFAULT_MATCH, array('controller' => 'page', 'slug' => 'home/about')),
            '/page/home/about.htm' => array_merge(Rx::$DEFAULT_MATCH, array('controller' => 'page', 'slug' => 'home/about', 'format' => 'htm')),
        );
        $routes['/admin/users/:id/edit'] = array(
            '/admin/users/23/edit' => array_merge(Rx::$DEFAULT_MATCH, array('controller' => 'admin', 'view' => 'edit', 'id' => '23')),
        );
        $routes['/admin/users/edit/:id'] = array(
            '/admin/users/edit/23' => array_merge(Rx::$DEFAULT_MATCH, array('controller' => 'admin', 'view' => 'edit', 'id' => '23')),
        );
        $routes['/posts/:year-:month-:day'] = array(
            '/posts/2012-03-27' => array_merge(Rx::$DEFAULT_MATCH, array('controller' => 'posts', 'year' => '2012', 'month' => '03', 'day' => '27')),
        );
        $routes['/posts/:slug(.:format)'] = array(
            '/posts/how-to-do' => array_merge(Rx::$DEFAULT_MATCH, array('controller' => 'posts', 'slug' => 'how-to-do')),
            '/posts/how-to-do.rss' => array_merge(Rx::$DEFAULT_MATCH, array('controller' => 'posts', 'slug' => 'how-to-do', 'format' => 'rss')),
            '/posts/Any Thing!' => array_merge(Rx::$DEFAULT_MATCH, array('controller' => 'posts', 'slug' => 'Any Thing!')),
        );
        //$routes['/api/user/:user_id/album/:album_id/photo/:photo_id.:format'] = array(
        //    '/api/user/default/album/vacation/photo/ASDFG.jpg' => array_merge(Rx::$DEFAULT_MATCH, array()),
        //);
        foreach($routes as $route => $tests) {
            foreach($tests as $path => $e) {
                $r = Rx::match($path, $route);
                $this->assertEquals($e, $r);
            }
        }
    }
}
