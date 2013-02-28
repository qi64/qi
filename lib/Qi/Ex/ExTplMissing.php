<?php

namespace Qi\Ex;

class ExTplMissing extends \DomainException
{
    public $file;
    public $vars;
}
