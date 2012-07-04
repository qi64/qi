<?php

namespace Qi\Spl;
use Qi\Traits\Spl\GenericIterator;

class FileIterator implements Iterator
{
    use GenericIterator;

    public function __construct($fp)
    {
        $this->fp = $fp;
    }

    public function each()
    {
        return fgets($this->fp);
    }

    protected function reset()
    {
        rewind($this->fp);
    }
}
