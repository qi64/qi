<?php

namespace Qi\Spl;
use FilterIterator, Iterator;

class CallbackFilterIterator extends FilterIterator
{
    const USE_FALSE = 0;  
    const USE_TRUE  = 1;  
    const USE_VALUE = 2;  
    const USE_KEY   = 3;  
    const USE_BOTH  = 4;  
    const REPLACE   = 0x00000001; 
    private $callback; 
    private $mode;     
    private $flags;    
    private $key;      
    private $current;  
    public function __construct(Iterator $it, $callback, $mode = self::USE_VALUE, $flags = 0)
    {
        parent::__construct($it);
        $this->callback = $callback;
        $this->mode     = $mode;
        $this->flags    = $flags;
    }

    public function accept()
    {
        $this->key     = parent::key();
        $this->current = parent::current();

        switch($this->mode) {
        default:
        case self::USE_FALSE;
            return false;
        case self::USE_TRUE:
            return true;
        case self::USE_VALUE:
            if($this->flags & self::REPLACE) {
                return (bool) call_user_func($this->callback, &$this->current);
            } else {
                return (bool) call_user_func($this->callback, $this->current);
            }
        case self::USE_KEY:
            if($this->flags & self::REPLACE) {
                return (bool) call_user_func($this->callback, &$this->key);
            } else {
                return (bool) call_user_func($this->callback, $this->key);
            }
        case self::USE_BOTH:
            if($this->flags & self::REPLACE) {
                return (bool) call_user_func($this->callback, &$this->current, &$this->key);
            } else {
                return (bool) call_user_func($this->callback, $this->current, $this->key);
            }
        }
    }

    function key()
    {
        return $this->key;
    }

    function current()
    {
        return $this->current;
    }

    function getMode()
    {
        return $this->mode;
    }

    function setMode($mode)
    {
        $this->mode = $mode;
    }

    function getFlags()
    {
        return $this->flags;
    }

    function setFlags($flags)
    {
        $this->flags = $flags;
    }
}
