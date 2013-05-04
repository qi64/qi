<?php

namespace Qi\Spl;
use IteratorAggregate,
    RecursiveDirectoryIterator,
    RecursiveIteratorIterator,
    FilesystemIterator,
    RegexIterator;

/**
 * Easy recursion on a File Tree with filter support
 */
class RecursiveDirIterator implements IteratorAggregate
{
    public $flags;
    public $mode;
    public $filter;
    public $dir;

    public function __construct($dir = null, $filter = null)
    {
        $this->flags = FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS;
        $this->dir = $dir;
        $this->filter = $filter;
    }

    public function getIterator()
    {
        $it = new RecursiveDirectoryIterator($this->dir, $this->flags);
        $it = new RecursiveIteratorIterator($it, $this->mode);

        if ($this->filter) {
            if (is_string($this->filter)) {
                $it = new RegexIterator($it, "!$this->filter!");
            }elseif (is_callable($this->filter)) {
                $it = new CallbackFilterIterator($it, $this->filter);
            }
        }

        return $it;
    }
}
