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
    public $mergeHeader = true;

    public function __construct($csv, $cfg = array())
    {
        if (is_string($csv)) {
            $csv = preg_split('!\r\n|\n|\r!', $csv, -1, PREG_SPLIT_NO_EMPTY);
        }
        foreach($cfg as $k => $v) $this->$k = $v;
        if ($csv && $this->hasHeader) {
            $header = array_shift($csv);
            $this->header = $this->getCsv($header);
        }
        parent::__construct($csv);
    }

    public function current()
    {
        $current = parent::current();
        $row = $this->getCsv($current);
        if ($this->header && $this->mergeHeader) {
            return array_combine($this->header, $row);
        }
        return $row;
    }

    public function tableizeHeader()
    {
        $this->header = array_map(array('Qi\Utils\Inflector', 'tableize'), $this->header);
    }

    protected function getCsv($row)
    {
        return str_getcsv($row, $this->delimiter, $this->enclosure, $this->escape);
    }
}
