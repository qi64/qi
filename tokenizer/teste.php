<?php

use Qi\Tokenizer\StandardizeIterator;

require_once dirname(__DIR__).'/lib/autoload.php';

$src = '<?php echo 123; ?>';
$it = new StandardizeIterator($src);
$it = new Qi\Tokenizer\IgnoreTokenIterator($it);
$it = new Qi\Spl\PrevNextIterator($it);
foreach($it as $current) {
    var_dump($current['content']);
}
