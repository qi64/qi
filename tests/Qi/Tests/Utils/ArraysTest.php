<?php

namespace Qi\Tests\Utils;
use Qi\Utils\Arrays;

class ArraysTest extends \PHPUnit_Framework_TestCase
{
    public function testCombineEqual()
    {
        $keys = explode(' ', 'a b c');
        $vals = explode(' ', '1 2 3');
        $e = array_combine($keys, $vals);
        $this->assertEquals($e, Arrays::combine($keys, $vals));
    }

    public function testCombineKeysLarger()
    {
        $keys = explode(' ', 'a b c');
        $vals = explode(' ', '1 2');
        $e = array('a' => '1', 'b' => '2', 'c' => null);
        $this->assertEquals($e, Arrays::combine($keys, $vals));
    }

    public function testCombineValuesLarger()
    {
        $keys = explode(' ', 'a b');
        $vals = explode(' ', '1 2 3');
        $e = array('a' => '1', 'b' => '2');
        $this->assertEquals($e, Arrays::combine($keys, $vals));
    }
}
