<?php
/*
 * Export all defaults tokens as an array
 */
$token_names = array();
for ($i = 128; $i < 512; $i++) {
    $name = token_name($i);
    if ($name == 'UNKNOWN') continue;
    $token_names[$i] = $name;
}

if (basename(@$argv[0]) == basename(__FILE__)) {
    var_export($token_names); // 127 tokens on php-5.4.12 (123 on 5.3.23)
    echo ' // ' . count($token_names);
}

return $token_names;
