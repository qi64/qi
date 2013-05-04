<?php

use Qi\Tokenizer\StandardizeIterator,
    Qi\Tokenizer\IgnoreTokenIterator,
    Qi\Spl\RecursiveDirIterator;

$ROOT = dirname(__DIR__);
require_once "$ROOT/lib/Qi/autoload.php";

$dir = new RecursiveDirIterator($ROOT, '\.php$');

foreach($dir as $file) {
    //echo $file.PHP_EOL;
}

$it = new StandardizeIterator(file_get_contents('bin/all_tokens.fixture.php'));
$it = new IgnoreTokenIterator($it);
$it->ignoreList = array(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT);

class TokenizerFormatter extends FilterIterator implements ArrayAccess
{
    public $formatters = array();
    public $defaultFormatter;

    public function current()
    {
        $current = parent::current();
        $code = $current['code'];

        if (isset($this->formatters[$code])) {
            foreach($this->formatters[$code] as $filter) {
                if (is_callable($filter)) {
                    $return = call_user_func($filter, $current);

                    if (is_string($return)) {
                        $current['content'] = $return;
                    }elseif (is_array($return)) {
                        $current = $return;
                    }elseif ($return === false) {
                        $current = $return;
                    }
                }
            }
        }

        if ($current && is_callable($this->defaultFormatter)) {
            $return = call_user_func($this->defaultFormatter, $current);
            if (is_string($return)) {
                $current['content'] = $return;
            }elseif (is_array($return)) {
                $current = $return;
            }
        }

        return $current;
    }

    public function offsetExists($offset)
    {
        return isset($this->formatters[$offset]);
    }

    public function &offsetGet($offset)
    {
        if ( ! isset($this->formatters[$offset]) ) {
            $this->formatters[$offset] = array();
        }

        return $this->formatters[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this[$offset][] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->formatters[$offset]);
    }

    public function __toString()
    {
        $str = "";
        foreach($this as $token) {
            $str .= $token['content'];
        }
        return $str;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Check whether the current element of the iterator is acceptable
     * @link http://php.net/manual/en/filteriterator.accept.php
     * @return bool true if the current element is acceptable, otherwise false.
     */
    public function accept()
    {
        return $this->current() !== false;
    }
}

class PackFormatter
{
    protected $indent = 0;

    public function __construct(TokenizerFormatter $formatter)
    {
        $formatter[T_OPEN_TAG]   = array($this, 'addSpaceAfter');
        $formatter[T_USE]        = array($this, 'addSpaceAfter');
        $formatter[T_NEW]        = array($this, 'addSpaceAfter');
        $formatter[T_CLASS]      = array($this, 'addSpaceAfter');
        $formatter[T_INTERFACE]  = array($this, 'addSpaceAfter');
        $formatter[T_TRAIT]      = array($this, 'addSpaceAfter');
        $formatter[T_GOTO]       = array($this, 'addSpaceAfter');
        $formatter[T_PUBLIC]     = array($this, 'addSpaceAfter');
        $formatter[T_PRIVATE]    = array($this, 'addSpaceAfter');
        $formatter[T_ABSTRACT]   = array($this, 'addSpaceAfter');
        $formatter[T_FINAL]      = array($this, 'addSpaceAfter');
        $formatter[T_STATIC]     = array($this, 'addSpaceAfter');
        $formatter[T_PROTECTED]  = array($this, 'addSpaceAfter');
        $formatter[T_CONST]      = array($this, 'addSpaceAfter');
        $formatter[T_CASE]       = array($this, 'addSpaceAfter');
        $formatter[T_FUNCTION]   = array($this, 'addSpaceAround');
        $formatter[T_IMPLEMENTS] = array($this, 'addSpaceAround');
        $formatter[T_EXTENDS]    = array($this, 'addSpaceAround');
        $formatter[T_AS]         = array($this, 'addSpaceAround');
        $formatter[T_INSTEADOF]  = array($this, 'addSpaceAround');
        $formatter[T_INSTANCEOF] = array($this, 'addSpaceAround');
        $formatter[T_LOGICAL_OR] = array($this, 'addSpaceAround');
        $formatter[T_LOGICAL_XOR]= array($this, 'addSpaceAround');
        $formatter[T_END_HEREDOC]= array($this, 'newLine');
        $formatter[T_INLINE_HTML]= false; // ignore
        /*
        $formatter[T_SEMICOLON]  = array($this, 'newLine');
        $formatter[T_OPEN_CURLY_BRACKET] = array($this, 'indentUp');
        $formatter[T_OPEN_CURLY_BRACKET] = array($this, 'newLine');
        $formatter[T_OPEN_CURLY_BRACKET] = array($this, 'addSpaceBefore');

        $formatter[T_CLOSE_CURLY_BRACKET] = array($this, 'newLine');
        $formatter[T_CLOSE_CURLY_BRACKET] = array($this, 'indentDown');
        */
    }

    public function indentUp()
    {
        $this->indent++;
    }

    public function indentDown()
    {
        $this->indent--;
    }

    public function indent()
    {
        return str_repeat("\t", $this->indent);
    }

    public function addSpaceAfter($token)
    {
        return trim($token['content']) . ' ';
    }

    public function addSpaceBefore($token)
    {
        return ' ' . $token['content'];
    }

    public function addSpaceAround($token)
    {
        return ' ' . $token['content'] . ' ';
    }

    public function newLine($token)
    {
        return $token['content'] . "\n".$this->indent();
    }
}


$it = new TokenizerFormatter($it);
new PackFormatter($it);
file_put_contents("temp.php", $it);
system("php54 -l temp.php");
//unlink("temp.php");

