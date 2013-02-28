<?php

namespace Qi\Router;
use Qi\Http\Path;

class PathAlias
{
    public $aliases = array();

    public function __construct($aliases = array())
    {
        $this->aliases = $aliases;
    }

    public function __invoke($env)
    {
        $env['original_path'] = $env['path'];
        $env['path'] = $this->alias($env['path']);
        return $env;
    }

    public function alias($path)
    {
        foreach($this->aliases as $rx => $alias) {
            $match = Rx::match($path, $rx);
            if ($match) {
                $alias = Rx::replace($alias, $match);
                return Path::format($alias);
            }
        }
        return $path;
    }
}