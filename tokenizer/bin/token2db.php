<?php

use Qi\Tokenizer\StandardizeIterator;

require_once __DIR__.'/../lib/autoload.php';

$it = new StandardizeIterator(file_get_contents(__FILE__));

$pdo = new PDO('mysql:host=127.0.0.1;dbname=testes', 'root', 'root');

foreach($it as $token) {
    print_r($token);
}
