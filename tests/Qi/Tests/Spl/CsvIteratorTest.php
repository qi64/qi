<?php

namespace Qi\Tests\Spl;
use Qi\Spl\CsvIterator;
use org\bovigo\vfs\vfsStream;

class CsvIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        vfsStream::setup();
        $csv = vfsStream::url('root/test.csv');
        $content = <<<C
a,b,c
1,2

C;
        file_put_contents($csv, $content);
        $this->it = new CsvIterator($csv);
    }

    public function testRewind()
    {
        $e = array('a' => '1', 'b' => '2', 'c' => null);
        $e = array($e);
        $this->assertEquals($e, iterator_to_array($this->it));
        $this->assertEquals($e, iterator_to_array($this->it));
    }

    public function testHasHeaderFalse()
    {
        $e = array(
            array(0 => 'a', 1 => 'b', 2 => 'c'),
            array(0 => '1', 1 => '2')
        );
        $it = clone $this->it;
        $it->hasHeader = false;
        $this->assertEquals($e, iterator_to_array($it));
        $this->assertEquals($e, iterator_to_array($it));
    }
}
