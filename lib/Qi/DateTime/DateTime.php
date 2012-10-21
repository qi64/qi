<?php

namespace Qi\DateTime;

class DateTime extends \DateTime
{
    const US = 'Y-m-d H:i:s';
    const BR = 'd/m/Y H:i:s';

    public $format = self::US;

    public function __toString()
    {
        return $this->format($this->format);
    }
}
