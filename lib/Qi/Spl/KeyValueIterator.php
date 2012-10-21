<?php

namespace Qi\Spl;

class KeyValueIterator extends \IteratorIterator
{
    public function current()
    {
        $current = parent::current();
        if (is_array($current)) {
            return end($current);
        }
    }

    public function key()
    {
        $current = parent::current();
        if (is_array($current)) {
            return reset($current);
        }
    }
}
