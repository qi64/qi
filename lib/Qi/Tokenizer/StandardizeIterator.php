<?php

namespace Qi\Tokenizer;
use ArrayIterator;

/**
 * Iterate over token_get_all, returning all tokens Standardize as array
 */
class StandardizeIterator extends ArrayIterator
{
    /**
     * Standard Token codes starts from this one
     */
    const FIRST_DEFAULT_TOKEN_CODE = 258;

    /**
     * $STRINGS codes are below this code
     */
    const MAX_STRING_CODE = 10;

    /**
     * Missing PHP constants for characters tokens.
     * The constant value is ord($char)
     * @var array
     */
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

    /**
     * constants for strings tokens.
     * The constant value starts from 1 and always below MAX_STRING_CODE
     * @var array
     */
    public static $STRINGS = array(
         'null'   => array(1, 'T_NULL')
        ,'false'  => array(2, 'T_FALSE')
        ,'true'   => array(3, 'T_TRUE')
        ,'self'   => array(4, 'T_SELF')
        ,'parent' => array(5, 'T_PARENT')
        ,'ticks'  => array(6, 'T_TICKS')
    );

    /**
     * filled by static::setup() from $STRINGS above
     * @var array
     */
    public static $STRINGS_CODE = array();

    /**
     * @TODO advance line based on newline chars from the lastToken
     * @var int
     */
    protected $lastLine = 1;

    /**
     * Every token has these keys
     * @var array
     */
    public $keys = array('code', 'content', 'line', 'type');

    /**
     * @param string $php_source_code
     */
    public function __construct($php_source_code)
    {
        static::setup(); // ensure initialization
        parent::__construct(token_get_all($php_source_code));
    }

    /**
     * @return the same array as token_get_all with the last element been the token name:
     * list($code, $content, $line, $name) = ->current()
     */
    public function current()
    {
        $current = parent::current();

        // single char token
        if ( ! is_array($current) ) {
            $current = array(ord($current), $current, $this->lastLine);
        }

        // string token
        if ( $current[0] == T_STRING && isset(static::$STRINGS[$current[1]]) ) {
            $current[0] = static::$STRINGS[$current[1]][0];
        }

        // append token name that's missing from starndard array
        $current[] = static::getTokenName($current[0]);

        // save current line for the next iteration
        $this->lastLine = $current[2];

        // assign key names to the index based standard array
        return array_combine($this->keys, $current);
    }

    public static function getTokenName($code)
    {
        // core PHP tokens
        if ($code >= static::FIRST_DEFAULT_TOKEN_CODE) {
            return token_name($code);
        }

        // false, true, null, self, parent, ...
        if ($code <= static::MAX_STRING_CODE) {
            return static::$STRINGS_CODE[$code];
        }

        // handle as a single char string token
        return static::$CHARS[chr($code)];
    }

    /**
     * Create at runtime the static::$STRINGS_CODE array from static::$STRINGS_CODE
     */
    public static function setup()
    {
        if ( ! empty(static::$STRINGS_CODE) ) return;

        // define global constants for chars
        foreach(static::$CHARS as $char => $const) {
            define($const, ord($char));
        }

        // define global constants for strings
        foreach(static::$STRINGS as $map) {
            static::$STRINGS_CODE[$map[0]] = $map[1];
            define($map[1], $map[0]);
        }
    }

    /**
     * iterator rewind resets line position
     */
    public function rewind()
    {
        $this->lastLine = 1;
        parent::rewind();
    }
}
