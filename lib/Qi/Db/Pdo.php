<?php

namespace Qi\Db;
use Qi\Utils\Arrays;

class Pdo extends \Pdo
{
    protected $dsn = '';
    public $lastQuery = '';
    public $queryHistory = array();
    public function __construct($dsn, $username = null, $passwd = null, $options = null)
    {
        parent::__construct($dsn, $username, $passwd, $options);
        $this->dsn = $dsn;
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('\Qi\Db\PdoStatement'));
    }

    public function prepare($statement, $driver_options = array())
    {
        $stmt = parent::prepare($statement, $driver_options);
        $this->queryHistory[] = $this->lastQuery = $stmt->queryString;
        return $stmt;
    }

    public function dropTable($name)
    {
        $this->exec("DROP TABLE IF EXISTS $name");
        return $this;
    }

    public function createTable($name, $columns = array())
    {
        if ( is_array($columns) ) {
            $fields = array();
            foreach($columns as $k => $v) {
                $fields[] = is_numeric($k) ? $v : "$k $v";
            }
            $fields = implode(",\n", $fields);
        }else{
            $fields = $columns;
        }
        $sql = "CREATE TABLE IF NOT EXISTS $name (\n$fields\n)";
        $this->exec($sql);
        return $this;
    }

    public function select($sql, $params = array())
    {
        $params = func_get_args();
        array_shift($params);
        $params = Arrays::flatten($params);
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Same as $pdo->select($sql, $id)->fetch();
     * @param $sql
     * @param array $params
     * @return mixed
     */
    public function queryFirstRow($sql, $params = array())
    {
        $stmt = call_user_func_array(array($this, 'select'), func_get_args());
        $row = $stmt->fetch();
        $stmt->closeCursor();
        if ($row === false) throw new \DomainException("row not found");
        return $row;
    }

    public function count($table, $where = '1')
    {
        return $this->queryValue("SELECT count(*) FROM $table WHERE $where");
    }

    public function queryValue($sql, $params = array())
    {
        $row = call_user_func_array(array($this, 'queryFirstRow'), func_get_args());
        return reset($row);
    }

    public function buildFieldValues($data)
    {
        $keys = array_keys($data);
        return array( implode(', ', $keys), implode(', :', $keys));
    }

    public function insert($table, $data, $or = null)
    {
        $this->prepareInsertOrReplace($table, $data, $or)->execute($data);
        return $this->lastInsertId();
    }

    public function update($table, $data, $where = '1', $whereData = array())
    {
        $fields = array();
        foreach($data as $k => $v) {
            $v = $this->quote($v);
            $fields[] = "$k = $v";
        }
        $fields = implode(",\n", $fields);

        if (is_array($where)) {
            $whereData = array();
            foreach($where as $k => $v) {
                $whereData[] = "$k = :$k";
            }
            $whereData = implode(" AND ", $whereData);
            list($where, $whereData) = array($whereData, $where);
        }else{
            $whereData = func_get_args();
            $whereData = array_slice($whereData, 3);
            $whereData = Arrays::flatten($whereData);
        }
        $sql = "UPDATE $table SET\n$fields\nWHERE $where";
        $stmt = $this->prepare($sql);
        $stmt->execute($whereData);
        return $stmt->rowCount();
    }

    public function replace($table, $data, $or = null)
    {
        $this->prepareInsertOrReplace($table, $data, $or, 'REPLACE')->execute($data);
        return $this->lastInsertId();
    }

    protected function prepareInsertOrReplace($table, $data, $or = null, $type = "INSERT")
    {
        if ($or) $or = " OR $or";
        $sql = vsprintf("$type$or INTO $table (%s) VALUES (:%s)", $this->buildFieldValues($data));
        return $this->prepare($sql);
    }
}
