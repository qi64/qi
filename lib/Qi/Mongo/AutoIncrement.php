<?php

namespace Qi\Mongo;

/**
 * Created by JetBrains PhpStorm.
 * User: neves
 * Date: 12/13/12
 * Time: 1:46 AM
 * To change this template use File | Settings | File Templates.
 */
class AutoIncrement
{
    public $collectionName;
    /**
     * @var \MongoCollection
     */
    protected $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
        $this->auto_increments = new Collection($collection->db, "auto_increments");
    }

    public function nextId()
    {
        $return = $this->auto_increments->findAndModify(
            array('_id' => $this->collection->getName()),
            array('$inc' => array('id' => 1)),
            array(),
            array('new' => true, 'upsert' => true)
        );
        return $return['value']['id'];
    }
}
