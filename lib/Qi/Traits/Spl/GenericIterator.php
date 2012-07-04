<?php

namespace Qi\Traits\Spl;

trait GenericIterator
{
    // @TODO test name collision
    protected $valid = false;
    protected $key = 0;
    protected $current = null;

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
        $this->reset();
        $this->next();
    }

    public function next()
    {
        $this->current = $this->each();
        $this->valid = $this->current !== null && $this->current !== false;
        $this->key = $this->valid ? $this->key + 1 : -1;
    }

    abstract protected function each();
    abstract protected function reset();
}
