<?php

namespace Qi\Html;

use ArrayIterator;
use Qi\Utils\Php;
use Qi\Utils\Uf;

class InputFactory
{
    public function __call($name, $args)
    {
        $attr = reset($args);
        $attr['type'] = $name;
        return $this->input($attr);
    }

    public function textarea($attr = array())
    {
        $e = new Block();
        $e->content = @$attr['content'];
        unset($attr['content']);
        $e->attr = new Attr($attr);
        return $e;
    }

    public function input($attr = array())
    {
        $default = array('type' => 'text');
        $attr = array_merge($default, $attr);
        $e = new Inline();
        $e->attr = new Attr($attr);
        return $e;
    }

    public function select($attr = array())
    {
        $e = new Block();
        $e->tag = "select";
        $e->attr = new Attr($attr);
        return $e;
    }

    public function uf($attr = array())
    {
        $select = $this->select($attr);
        $select->content = new Options(Uf::$NOMES);
        return $select;
    }

    public function map($attr = array())
    {
        return <<<H
<div class="control-map row-fluid">
    <div class="span6">
        <label>Endereço:</label>
        <input type="text" class="input-block-level">

        <label>Zoom:</label>
        <input type="range" min="14" max="17">
    </div>

    <input type="hidden" name="" value="">

    <div class="span6">
        <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
            src="https://maps.google.com/maps?q=maringa&z=16&output=embed&iwloc=near">
        </iframe>
    </div>
</div>
H;

    }
}

class Options extends ArrayIterator
{
    public function __toString()
    {
        $options = '';
        foreach($this as $k => $option) {
            $options .= "\n\t".$this->convertOption($k, $option);
        }
        return "$options\n";
    }

    protected function convertOption($k, $option)
    {
        if ( is_array($option) ) {
            return $this->convertOption(reset($option), end($option));
        }
        if ( is_callable($option) ) {
            return $this->convertOption($k, $option($option, $k));
        }

        $opt = new Option();
        $opt->value = $k;
        $opt->content = $option;
        return $opt;
    }
}

/**
 * @TODO separate a single Attr pair from a collection of Attr
 * Class Attr
 * @package Qi\Html
 */
class Attr extends ArrayIterator
{
    public static $format = ' %s="%s"';
    protected $_format;

    public function getFormat()
    {
        return $this->_format ?: static::$format;
    }

    public function setFormat($format)
    {
        $this->_format = $format;
    }

    public function __set($k, $v) {        $this->offsetSet($k, $v); }
    public function __get($k    ) { return $this->offsetGet($k    ); }
    public function __isset($k  ) { return $this->offsetExists($k ); }
    public function __unset($k  ) {        $this->offsetUnset($k  ); }

    public function __toString()
    {
        $pairs = array();
        foreach($this as $k => $v) {
            if ($v === null) continue; // ignore null values. What about false?
            if ( is_array($v) ) $v = implode(' ', $v);
            // ignore function callback
            if ( is_callable($v) && ! Php::isInternalFunction($v) ) $v = $v($this);
            if ($v === null) continue;
            $k = $this->h($k);
            $v = $this->h($v);
            $pairs[] = sprintf($this->getFormat(), $k, $v);
        }
        return implode('', $pairs);
    }

    protected function h($s)
    {
        return htmlentities($s, ENT_QUOTES, "UTF-8");
    }
}


class Inline
{
    public $tag = 'input';
    public $format = '<%s%s>';
    public $attr = "";
    public $content = "";

    public function __toString()
    {
        return sprintf($this->format, $this->tag, $this->attr, $this->content);
    }
}


class Block extends Inline
{
    public $tag = 'textarea';
    public $format = '<%s%s>%s</%1$s>';
}


class Option extends Block
{
    public $tag = 'option';
}


class Control
{
    public $label;
    public $help;

    public function __toString()
    {
        $e = new Inline();
        $e->attr = new Attr();
        return (string)$e;
    }
}
