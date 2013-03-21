<?php
/**
 * Created by JetBrains PhpStorm.
 * User: neves
 * Date: 3/16/13
 * Time: 12:21 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Qi\Html;

class Safe implements ISafe
{
    protected $str = '';

    public function __construct($str)
    {
        $this->str = $str;
    }

    public function __toString()
    {
        return (string)$this->str;
    }
}
