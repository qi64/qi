<?php

namespace Qi\Controller;
use Qi\Utils\Error,
    Qi\Http\Path;

class Site
{
    public function run()
    {
        $path = Path::current() ?: 'home';
        ob_start();
        Error::disable(E_NOTICE);
        include "paginas/$path.php";
        Error::pop();
        $TPL = ob_get_clean();

        ob_start();
        Error::disable(E_NOTICE);
        include 'paginas/_layout.php';
        Error::pop();
        return ob_get_clean();
    }
}
