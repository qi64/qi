<?php

namespace Qi\Spl;
use ArrayIterator;

class StrCsvIterator extends ArrayIterator
{
    public $delimiter = ',';
    public $enclosure = '"';
    public $escape = '\\';
    public $hasHeader = true;
    public $header = array();

    public function __construct($csv, $cfg = array())
    {
        if (is_string($csv)) {
            $csv = preg_split('!\r\n|\n|\r!', $csv, -1, PREG_SPLIT_NO_EMPTY);
        }
        foreach($cfg as $k => $v) $this->$k = $v;
        parent::__construct($csv);
    }

    public function current()
    {
        $current = parent::current();
        return str_getcsv($current, $this->delimiter, $this->enclosure, $this->escape);
    }
}
