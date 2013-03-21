<?php

namespace Qi\Controller\Step;
use Qi\Http\Header;

class PhpInfo
{
    public function __invoke($env)
    {
        // @TODO enable only on dev/debug
        if ($env->path == 'phpinfo') {
            Header::html();
            phpinfo();
            exit;
        }
    }
}
