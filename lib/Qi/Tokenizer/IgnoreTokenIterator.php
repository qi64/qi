<?php

namespace Qi\Tokenizer;
use Iterator, FilterIterator;

class IgnoreTokenIterator extends FilterIterator
{
    protected $ignoreList = array();

    public function __construct(Iterator $it, $ignoreList = array())
    {
        if (!$ignoreList) $ignoreList = array(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT);
        $this->ignoreList = $ignoreList;
        parent::__construct($it);
    }

    public function accept()
    {
        list($code) = array_values( $this->current() );
        return ! in_array($code, $this->ignoreList);
    }
}
