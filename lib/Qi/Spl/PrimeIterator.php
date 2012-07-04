<?php

namespace Qi\Spl;
use Qi\Traits\Spl\GenericIterator;

class PrimeIterator implements Iterator
{
    use GenericIterator;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    protected function each()
    {
        for ($i = $this->current + 1; $i <= $this->to; $i++)
            if ( $this->isPrime($i) ) return $i;
        return null; // @TODO return null or throw exception?
    }

    protected function reset()
    {
        $this->current = $this->from - 1;
    }

    protected function isPrime($n)
    {
        if ($n < 2) return false;
        for ($i = 2; $i < $n; $i++)
            if ($n % $i == 0) return false;
        return true;
    }
}
