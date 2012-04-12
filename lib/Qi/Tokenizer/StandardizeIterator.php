<?php

namespace Qi\Tokenizer;
use ArrayIterator;
/**
 * Iterate over token_get_all, returning all tokens Standardize as array
 */
class StandardizeIterator extends ArrayIterator
{
    const FIRST_DEFAULT_TOKEN_CODE = 258;
    const MAX_STRING_CODE = 10;

    public static $CHARS = array(
         "=" => 'T_ASSIGN'
        ,'"' => 'T_DOUBLE_QUOTED_STRING'
        ,":" => 'T_COLON'
        ,";" => 'T_SEMICOLON'
        ,"," => 'T_COMMA'
        ,"." => 'T_CONCAT'
        ,"(" => 'T_OPEN_PARENTHESIS'
        ,")" => 'T_CLOSE_PARENTHESIS'
        ,"[" => 'T_OPEN_SQUARE_BRACKET'
        ,"]" => 'T_CLOSE_SQUARE_BRACKET'
        ,"{" => 'T_OPEN_CURLY_BRACKET'
        ,"}" => 'T_CLOSE_CURLY_BRACKET'
        ,"+" => 'T_PLUS'
        ,"-" => 'T_MINUS'
        ,"*" => 'T_MULTIPLY'
        ,"/" => 'T_DIVIDE'
        ,"%" => 'T_MODULUS'
        ,"!" => 'T_BOOLEAN_NOT'
        ,"<" => 'T_IS_LESS_THAN'
        ,">" => 'T_IS_GREATER_THAN'
        ,"&" => 'T_BITWISE_AND'
        ,"|" => 'T_BITWISE_OR'
        ,"^" => 'T_BITWISE_XOR'
        ,"@" => 'T_ERROR_CONTROL'
    );

    public static $STRINGS = array(
         'null'   => array(1, 'T_NULL')
        ,'false'  => array(2, 'T_FALSE')
        ,'true'   => array(3, 'T_TRUE')
        ,'self'   => array(4, 'T_SELF')
        ,'parent' => array(5, 'T_PARENT')
        ,'ticks'  => array(6, 'T_TICKS')
    );

    // filled by self::setup() from $STRINGS above
    public static $STRINGS_CODE = array();
    // @TODO advance line based on newline chars from the lastToken
    protected $lastLine = 1;
    public $keys = array('code', 'content', 'line', 'type');

    public function __construct($php_source_code)
    {
        parent::__construct(token_get_all($php_source_code));
    }

    /**
     * @return the same array as token_get_all with the last element been the token name:
     * list($code, $content, $line, $name) = ->current()
     */
    public function current()
    {
        $current = parent::current();

        if ( ! is_array($current) ) { // single char token
            $current = array(ord($current), $current, $this->lastLine);
        }

        if ( $current[0] == T_STRING && isset(self::$STRINGS[$current[1]]) ) {
            $current[0] = self::$STRINGS[$current[1]][0];
        }

        $current[] = self::getTokenName($current[0]);
        $this->lastLine = $current[2];

        return array_combine($this->keys, $current);
    }

    public static function getTokenName($code)
    {
        // core PHP tokens
        if ($code >= self::FIRST_DEFAULT_TOKEN_CODE) {
            return token_name($code);
        }

        // false, true, null, self, parent, ...
        if ($code <= self::MAX_STRING_CODE) {
            return self::$STRINGS_CODE[$code];
        }

        // handle as a single char string token
        return self::$CHARS[chr($code)];
    }

    /**
     * Create at runtime the self::$STRINGS_CODE array from self::$STRINGS_CODE
     */
    public static function setup()
    {
        if ( ! empty(self::$STRINGS_CODE) ) return;
        foreach(self::$STRINGS as $map) {
            self::$STRINGS_CODE[$map[0]] = $map[1];
        }
    }

    public function rewind()
    {
        $this->lastLine = 1;
        parent::rewind();
    }
}

StandardizeIterator::setup();
