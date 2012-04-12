<?php

namespace Qi\Tests\Utils;
use Qi\Utils\Html;

class HtmlTest extends \PHPUnit_Framework_TestCase
{
    public function testPrettyPrint()
    {
        $htm = <<<H
<!DOCTYPE html>
<script>
window.onload = function(){};
</script>
<ul><li><?= 'foo' ?></li>
<li>b</li></ul>
H;
        Html::pp($htm);
    }
}
