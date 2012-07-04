<?php

namespace Qi\Spl;

class PregSliceStringIterator extends PregMatchIterator
{
    protected function each()
    {
        if ($this->offset >= strlen($this->subject)) {
            return $this->stopIteration();
        }

        $start = $this->offset;
        if ( ! $current = parent::each() ) {
            $slice = substr($this->subject, $this->offset);
            $this->offset = strlen($this->subject);
            return array(null, $start, $slice);
        }

        $current[] = substr($this->subject, $start, $current[1] - $start);

        return $current;
    }
}
