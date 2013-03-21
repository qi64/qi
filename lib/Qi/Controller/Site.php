<?php

namespace Qi\Controller;

use Qi\Utils\Error,
    Qi\Http\Path,
    Qi\Utils\Html;

class Site
{
    public function run()
    {
        $path = Path::current() ?: 'home';
        $tplPath = "$path.php";
        $TPL = Html::renderFile($tplPath) ?: Html::renderFile("404.php");

        $tplPath = "_layout.php";
        $vars = array('TPL' => $TPL);
        return Html::renderFile($tplPath, $vars) ?: "Faltando layout: $tplPath";
    }
}
