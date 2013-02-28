<?php

namespace Qi\Controller;

class Site
{
    public function run()
    {
        ob_start();
        include 'paginas/_layout.php';
        return ob_get_clean();
    }
}