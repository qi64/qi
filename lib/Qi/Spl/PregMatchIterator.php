<?php

namespace Qi\Spl;

class PregMatchIterator extends AbstractIterator
{
    public $offset = 0;
    public $subject = '';
    public $pattern = '//';

    protected function each()
    {
        $matches = array();
        preg_match($this->pattern, $this->subject, $matches, PREG_OFFSET_CAPTURE, $this->offset);
        if ( empty($matches) ) return $this->stopIteration();
        $match = $matches[0]; // only one result at the 0 index
        $this->offset = $match[1] + strlen($match[0]); // set offset after match
        return $match;
    }

    protected function reset()
    {
        $this->offset = 0;
    }
}
