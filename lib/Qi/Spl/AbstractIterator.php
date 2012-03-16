<?php

namespace Qi\Spl;
use Iterator;

/**
 * Super easy Iterator implementation.
 * Just implement the each method
 */
abstract class AbstractIterator implements Iterator
{
    protected $valid = false;
    protected $key = -1;
    protected $current = null;
    protected $stopval = null;

    protected function stopIteration()
    {
        return $this->stopval;
    }

    public function current()
    {
        return $this->current;
    }

    public function valid()
    {
        return $this->valid;
    }

    public function key()
    {
        return $this->key;
    }

    public function rewind()
    {
        $this->key = -1;
        $this->valid = false;
        $this->current = $this->stopval;
        $this->reset();
        $this->next();
    }

    public function next()
    {
        $this->current = $this->each();
        $this->valid = $this->current !== $this->stopval;
        $this->key = $this->valid ? $this->key + 1 : -1;
    }

    abstract protected function each();
    protected function reset(){}
}
