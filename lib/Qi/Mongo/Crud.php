<?php

namespace Qi\Mongo;

/**
 * Created by JetBrains PhpStorm.
 * User: neves
 * Date: 12/13/12
 * Time: 4:13 PM
 * To change this template use File | Settings | File Templates.
 */
class Crud extends \MongoCollection
{
    /**
     * @var \MongoDB
     */
    public static $mongoDB;

    public static function with($collectionName)
    {
        return new self(self::getMongoDB(), $collectionName);
    }

    public static function getMongoDB()
    {
        return self::$mongoDB;
    }
}
