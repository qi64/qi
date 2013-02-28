<?php

namespace Qi\Ex;

class ExRender extends \DomainException
{
    public $file;
    public $vars;
    public $output;
}
