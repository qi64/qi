<?php

namespace Qi\Tests\Spl;
use Qi\Spl\AbstractIterator;

class AbstractIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testRewind()
    {
        $stub = $this->getMockForAbstractClass('Qi\Spl\AbstractIterator');
        $stub->expects($this->any())->method('each')->will($this->returnValue(1));
        $this->assertNull($stub->current());
        $this->assertEquals(-1, $stub->key());
        $this->assertFalse($stub->valid());

        $stub->rewind();
        $this->assertEquals(1, $stub->current());
        $this->assertEquals(0, $stub->key());
        $this->assertTrue($stub->valid());
    }
}