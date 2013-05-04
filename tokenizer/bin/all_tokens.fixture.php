<?php

/**
 * list of all tokens: http://php.net/manual/en/tokens.php
 * T_CHARACTER not used anymore
 * T_BAD_CHARACTER anything below ASCII 32 except \t (0x09), \n (0x0a) and \r (0x0d)
 * The constants values are not consistents between PHP versions,
 * The first token is 258 => 'T_REQUIRE_ONCE'
 * The last is 384 => 'T_NS_SEPARATOR'
 * total of 127 tokens
 * Use numbers between 1 and 256 to represent new custom tokens,
 * or use the respective ascii code.
 * T_STRING is used for all identifiers names: class, interface, functions, traits, gotoLabels, stdClass, etc
 * T_VARIABLE is used for properties too
 * https://github.com/sebastianbergmann/php-token-stream
 */

namespace Foo\Bar;

include 'dummy.php';
require 'dummy.php';
include_once 'dummy.php';
require_once 'dummy.php';

trait MyTrait  { function foo(){} }
trait YourTrait { function foo(){} }
goto label;
label:
abstract class ClasseAbstrata extends \stdClass {
    const c = 0;
    use MyTrait, YourTrait {
        MyTrait::foo insteadof YourTrait;
    }
};
interface Interf {};
final class Classe extends ClasseAbstrata implements Interf {

    public static $public = 'Public';
    protected $protected = 'Protected';
    private $private = 'Private';
    var $v = 'v';

    function &f (Array $p1, String $p2, Int $p3, Callable $c) {
        global $v;
        __DIR__;
        __TRAIT__;
        __NAMESPACE__;
        __FILE__;
        __LINE__;
        __CLASS__;
        __METHOD__;
        self::$public;
        parent::c;
        return __FUNCTION__;
    }
};
$obj = @new Classe();
isset($obj->v);
$obj = clone $obj;
$obj = (object) $obj;

list($v) = (int)2;
$v += 1; $v -= 1; $v *= 2; $v /= 1;
$v = $v*(1+0-1/1)%1;
$v++; $v--;
$v &= 1; $v = $v & 1;
$v |= 3; $v = $v | 1;
$v ^= 2; $v = $v ^ 1;
$v <<= 1; $v << 1;
$v >>= 3; $v >> 3;
$v %= (double)1.0;
$b = 1 < 2; $b = 1 > 2; $b = 1 <= 2; $b = 1 >= 2;
$n = null;
empty($v);

// comentátio

$a1 = array( array( 'key' => 'value', 'KEY' => "VALUE" ));
foreach ($a1 as $a2): $a = (array)$a2; break; endforeach;
for(;false;): eval(""); endfor;

do { continue; } while (0 >= 1);
while (1 <= 0): exit(); die(); endwhile;

$s = <<<Heredoc
text";
Heredoc;
$s = <<<'nowdoc'
text";
nowdoc;
$s .= "other {$v} 'teste' \"teste\" $a[key] ${v}". (String)Classe::c . 'single quotes';

$exp = (bool)((true == true) && (true === true) || (true != false)
    and (true !== false) or true xor (!false));

try { throw new \Exception(); }
catch (\Exception $e) {
    if (!($e instanceof \Exception)):
        print "";
    elseif (false):
        echo "";?> <?= ""; ?> <?
    else:
    endif;
}

switch ($v):
    case 0:
        break;
    default:
        break;
endswitch;

unset($v);
(unset)$s;
declare(ticks=1): enddeclare;
__halt_compiler(); 
