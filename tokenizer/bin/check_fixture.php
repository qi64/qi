<?php


$sample = __DIR__.'/all_tokens.fixture.php';
require_once $sample; // just test if fixture is a valid PHP code

$defaults = require_once __DIR__.'/defaults.php';

$all_tokens = token_get_all( file_get_contents($sample) );

$noname = array();
$found_tokens = array();

foreach($all_tokens as $token) {
    if ( ! is_array($token) ) {
        $noname[$token] = null;
        continue;
    }
    $found_tokens[$token[0]] = $name = token_name($token[0]);
    $content = trim($token[1]);
    if ($token[0] != T_WHITESPACE)
        echo "$name\t `$content`\n";
}

$noname = array_keys($noname);
var_export($noname);

var_export(array_diff($defaults, $found_tokens));
