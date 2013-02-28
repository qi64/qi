<?php

namespace Qi\Spl;

/**
$permut = new PermutationIterator();
$permut->append(range(1, 2));
$permut->append(range(1, 2));

foreach($permut as $k => $p) {
    echo "$k: $permut";
}
# outputs:
1: 1 1
2: 2 1
3: 1 2
4: 2 2
 */

class PermutationIterator Implements \Iterator, \Countable
{
    protected $lists = array();
    protected $k = 1;

    public $reverse = false;

    public function append($list)
    {
        if (is_array($list)) $list = new \ArrayIterator($list);
        if (! $list instanceof \Iterator) throw new \DomainException('list must be an Iterator!');
        $this->lists[] = $list;
    }

    public function rewind()
    {
        $this->k = 1;
        foreach($this->lists as $list) {
            $list->rewind();
        }
    }

    public function current()
    {
        $currents = array();
        foreach($this->lists as $list) {
            $currents[] = $list->current();
        }
        return $this->reverse ? array_reverse($currents) : $currents;
    }

    public function next()
    {
        $this->k++;
        $this->cycleLists($this->lists);
    }

    /**
     * Recursive method
     * @param $lists
     * @return bool true to rewind and keep permutating
     */
    protected function cycleLists($lists)
    {
        if (!$lists) return false;
        $current = array_shift($lists);
        $current->next();
        if ( $current->valid() ) {
            return true;
        }elseif( $this->cycleLists($lists) ) {
            // if the next lists can cycle, rewind myself
            $current->rewind();
            return true;
        }else{
            return false;
        }
    }

    public function key()
    {
        return $this->k;
    }

    public function valid()
    {
        foreach($this->lists as $list) {
            if ( $list->valid() ) return true;
        }
        return false;
    }

    public function __toString()
    {
        return implode(' ', $this->current()) . PHP_EOL;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        $counts = array();
        foreach($this->lists as $list) {
            $counts[] = count($list);
        }
        return array_reduce($counts, function($a, $b) { return $a * $b; }, 1);
    }
}
