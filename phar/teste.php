<?php

require_once 'qi.phar'; // register autoload
// require_once 'phar://qi.phar/autoload.php'; // same as above

$db = new Qi\Db\Sqlite(":memory:");
