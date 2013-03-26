<?php

namespace Qi\Ex;

use PDOException;

class ExPdo extends PDOException {
    public $sql;
    public function __construct($sql, PDOException $previous)
    {
        parent::__construct($previous->getMessage(), 0, $previous);
        $this->sql = $sql;
    }

    public function __toString()
    {
        return "\n".$this->sql."\n\n".$this->getMessage()."\n\n".$this->getTraceAsString();
    }
}
