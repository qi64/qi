<?php

namespace Qi\Spl;
/**
 * Returns the key as the current
 */
class KeyIterator extends \IteratorIterator
{
    function current()
    {
        return $this->key();
    }
}
