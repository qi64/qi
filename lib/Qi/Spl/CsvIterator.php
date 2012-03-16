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
    public $hasHeader = true;
    public $header = array();

    protected $fp;

    public function __construct($fp, $cfg = array())
    {
        $this->setFile($fp);
        foreach($cfg as $k => $v) $this->$k = $v;
    }

    public function setFile($fp)
    {
        $this->fp = is_resource($fp) ? $fp : fopen($fp, 'r');
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

    /**
     * need to rewind the file pointer
     */
    protected function reset()
    {
        rewind($this->fp);
    }

    /**
     * Skip first line if it's header
     */
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
