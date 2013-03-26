<?php

namespace Qi\Db;

use PDOException;
use Qi\Ex\ExPdo;

class PdoStatement extends \PDOStatement
{
    public function execute($input_parameters = null)
    {
        try {
            parent::execute($input_parameters);
        }catch(PDOException $e) {
            throw new ExPdo($this->queryString, $e);
        }
    }
}
