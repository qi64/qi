<?php
/**
 * @todo better Closure support
 */
class DecoratingIterator extends \IteratorIterator
{
    private $decorator;
    private $current_key;
    function __construct( $childIterator, $decorator ){
        $this->decorator = $decorator;
        parent::__construct( $childIterator );
    }
    function current(){
        $decorator = $this->decorator;
        if( is_callable($decorator) ){
            return call_user_func( $decorator, parent::current() );
        } elseif( class_exists($decorator) ){
            return new $decorator( parent::current() );
        } else {
            throw new \Exception( 'Not a valid decorator: '.var_export($decorator,true) );
        }
    }
}
