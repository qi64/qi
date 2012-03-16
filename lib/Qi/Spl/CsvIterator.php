<?php

namespace Qi\Spl;
use Qi\Utils\Arrays;
use Qi\Spl\AbstractIterator;

class CsvIterator extends AbstractIterator
{
    public $length = null;
    public $delimiter = ',';
    public $enclosure = '"';
    public $escape = '\\';

    protected $hasHeader = true;
    protected $header = array();
    protected $fp;

    public function __construct($fp, $hasHeader = true)
    {
        if ( is_resource($fp) ) {
            $this->fp = $fp;
        }
        $this->hasHeader = $hasHeader;
    }

    public function each()
    {
        $row = fgetcsv($this->fp, $this->length, $this->delimiter, $this->enclosure, $this->escape);
        if ($row === false) return $this->stopIteration();
        return $this->mergeHeader($row);
    }

    protected function mergeHeader($row)
    {
        if ( ! $this->header ) return $row;
        return Arrays::combine($this->header, $row);
    }

    public function rewind()
    {
        parent::rewind();
        if ( ! $this->hasHeader ) return;
        if ( $this->valid() ) {
            $this->header = $this->current();
            $this->key--;
            $this->next();
        }
    }
}
