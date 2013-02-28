<?php

namespace Qi\Mongo;

/**
 * Created by JetBrains PhpStorm.
 * User: neves
 * Date: 12/13/12
 * Time: 1:43 AM
 * To change this template use File | Settings | File Templates.
 */
class Collection extends \MongoCollection
{
    protected static $cacheIdGenerator;

    public function get($id)
    {
        $data = array('_id' => $id);
        $data = $this->ensureNumericId($data);
        return $this->findOne($data);
    }

    public function set($id, $data)
    {
        return $this->update(array('_id' => $id), array('$set' => $data));
    }

    public function update($criteria , array $newobj, array $options = array())
    {
        if ( ! is_array($criteria) ) {
            $criteria = array('_id' => $criteria);
        }
        $criteria = $this->ensureNumericId($criteria);
        $newobj = $this->convertId($newobj);
        $newobj = $this->ensureNumericId($newobj);
        parent::update($criteria, $newobj, $options);
    }

    /**
     * Se o campo _id existir, realiza um update, caso contrário um insert.
     * Lembrando que o update do mongo SUBSTITUI o registro inteiro.
     * @link http://www.php.net/manual/en/mongocollection.save.php
     * @param mixed $data Array to save.
     * @param array $options Options for the save.
     * @throws \MongoCursorException
     * @return mixed
     */
    public function save(array $data, array $options = array())
    {
        $data = $this->handleId($data);
        $id = $data['_id'];
        parent::save($data, $options);
        return $id;
    }

    public function delete($id)
    {
        $data = array("_id" => $id);
        $data = $this->ensureNumericId($data);
        $this->remove($data);
    }

    public function findAndModify(array $query, array $update = array(), array $fields = array(), array $options = array())
    {
        $cmd = array(
            'query' => $query,
            'update' => $update,
            'new' => @$options['new'],
            'upsert' => @$options['upsert']
        );
        $cmd = array_merge(array('findAndModify' => $this->getName()), $cmd);
        return $this->db->command($cmd);
    }

    protected function handleId($data)
    {
        $data = $this->convertId($data);
        $data = $this->ensureNumericId($data);
        $data = $this->createId($data);
        return $data;
    }

    protected function convertId($data)
    {
        if ( isset($data['id']) ) {
            $data['_id'] = $data['id'];
            unset($data['id']);
        }
        return $data;
    }

    protected function ensureNumericId($data)
    {
        if ( isset($data['_id']) && is_numeric($data['_id'])) {
            $data['_id'] = intval($data['_id']);
        }
        return $data;
    }

    protected function createId($data)
    {
        if ( ! @$data['_id'] ) {
            $data['_id'] = $this->getIdGenerator()->nextId();
        }
        return $data;
    }

    protected function getIdGenerator()
    {
        return self::$cacheIdGenerator ?: self::$cacheIdGenerator = new AutoIncrement($this);
    }
}
