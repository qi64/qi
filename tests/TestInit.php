<?php

$ROOT = dirname(__DIR__);
require_once "$ROOT/lib/autoload.php";
require_once "$ROOT/ClassLoader/UniversalClassLoader.php";

$loader = new Symfony\Component\ClassLoader\UniversalClassLoader();
$loader->registerNamespace('org\\bovigo\\vfs', "$ROOT/vfsStream/src/main/php");
$loader->register();
