<?php

namespace Qi\Db;

class Sqlite extends Pdo
{
    public function __construct($path = ':memory:')
    {
        return parent::__construct("sqlite:$path");
    }
}
