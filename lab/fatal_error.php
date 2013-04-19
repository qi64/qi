<?php

use Qi\Utils\Php;

require_once '../lib/Qi/autoload.php';

//var_dump(E_ERROR);

foreach(range(1, 10) as $i) {
	echo foo;
}

echo "\nantes ob_start\n";

Php::ob_start();

function teste() {
	throw new Exception("teste");
	$foo = null;
	$foo->bar();
	return teste;
}

echo "\nantes fatal\n";

//echo bar;

//teste();

echo "\ndepois\n";
