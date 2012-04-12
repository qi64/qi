<?php

namespace Qi\Spl;
use FilterIterator,
    RecursiveCallbackFilterIterator,
    IteratorAggregate,
    RecursiveRegexIterator,
    RecursiveDirectoryIterator,
    RecursiveIteratorIterator;

/**
 * Easy recursion on a File Tree
 */
class RecursiveDirIterator implements IteratorAggregate
{
    public $flags;
    public $mode;
    public $filter;
    public $dir;

    public function __construct($dir = null)
    {
        $this->dir = $dir;
    }

    public function getIterator()
    {
        $it = new RecursiveDirectoryIterator($this->dir, $this->flags);
        if ($this->filter) {
            if (is_string($this->filter)) {
                $it = new RecursiveRegexIterator($it, $this->filter);
            }elseif (is_callable($this->filter)) {
                $it = new RecursiveCallbackFilterIterator($it, $this->filter);
            }
        }
        return new RecursiveIteratorIterator($it, $this->mode, $this->flags);

        return $it;
    }
    public function key()
    {
        return substr(parent::key(), strlen($this->dir) + 1);
    }
}