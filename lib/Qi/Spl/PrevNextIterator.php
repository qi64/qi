<?php
// @TODO create an iterator that can continue an iterator from the point of creation to the end (and maybe reverse)
namespace Qi\Spl;
use Iterator, CachingIterator;

class PrevNextIterator extends CachingIterator
{
    protected $prev = null;

    public function getPrev()
    {
        return $this->prev;
    }

    public function getNext()
    {
        return $this->hasNext()
               ? $this->getInnerIterator()->current()
               : null;
    }

    public function rewind()
    {
        $this->prev = null;
        return parent::rewind();
    }

    public function next()
    {
        $this->prev = $this->current();
        return parent::next();
    }
}