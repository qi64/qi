<?php

use Qi\Spl\RecursiveDirIterator;

require_once dirname(__DIR__).'/lib/autoload.php';

$it = new RecursiveDirIterator(dirname(__DIR__));
$it->mode = RecursiveIteratorIterator::SELF_FIRST;
$it->filter = function($current, $key, $iterator) {
    $ignore = ['.git', '.idea'];
    return ! in_array(basename($key), $ignore);
};

$it = $it->getIterator();
// filtering files after recursive iteration
$it = new RegexIterator($it->getIterator(), '/\.php/');
foreach($it as $info) {
    var_dump($it->getSubPathName());
}
