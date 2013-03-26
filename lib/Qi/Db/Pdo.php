<?php

/**
 * Based on http://www.meekro.com/
 */
namespace Qi\Db;
use PDOException;
use Qi\Ex\ExPdo;
use Qi\Utils\Arrays;

class Pdo extends \PDO
{
    protected static $instance;
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
        //$this->setAttribute(Pdo::MYSQL_ATTR_USE_BUFFERED_QUERY, false); // permite executar query com milhares de registros
        //$this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // retorna erro ao preparar a query ao invés de apenas quando executá-la.
    }

    public static function dsn($host, $db)
    {
        return "mysql:host=$host;dbname=$db;charset=utf8";
    }

    /**
     * @param $host
     * @param $db
     * @param $username
     * @param $passwd
     * @return Pdo
     */
    public static function connect($host, $db, $username, $passwd)
    {
        if (self::$instance) return self::$instance;
        $class = get_called_class();
        return self::$instance = new $class(self::dsn($host, $db), $username, $passwd);
    }

    public function prepare($statement, $driver_options = array())
    {
        try {
            $stmt = parent::prepare($statement, $driver_options);
        }catch(PDOException $e) {
            throw new ExPdo($statement, $e);
        }
        // desligar o queryHistory quando for fazer muitos prepare, para não estourar a memória
        $this->queryHistory[] = $this->lastQuery = $stmt->queryString;
        return $stmt;
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
        //if ($row === false) throw new \DomainException("row not found");
        return $row;
    }

    public function queryValue($sql, $params = array())
    {
        $row = call_user_func_array(array($this, 'queryFirstRow'), func_get_args());
        return reset($row);
    }

    public function count($table, $where = '1')
    {
        return $this->queryValue("SELECT count(*) FROM `$table` WHERE $where");
    }

    /**
     * Retorna um array de valores ao invés de rows
     * @param $sql
     * @param array $params
     * @return array
     */
    public function queryValues($sql, $params = array())
    {
        $stmt = call_user_func_array(array($this, 'select'), func_get_args());
        $values = array();
        foreach($stmt as $row) {
            $values[] = reset($row);
        }
        return $values;
    }

    public function buildFieldValues($keys)
    {
        if ($keys != array_values($keys)) {
            $keys = array_keys($keys);
        }
        return array( implode(', ', $keys), implode(', :', $keys));
    }

    public function save($table, $data)
    {
        if ( isset($data['id']) ) {
            $count = $this->update($table, $data, array('id' => $data['id']));
            if (!$count) $this->insert($table, $data);
        }else{
            $this->insert($table, $data);
        }
    }

    public function insert($table, $data, $or = null)
    {
        $stmt = $this->prepareInsertOrReplace($table, $data, $or);
        $stmt->execute($data);
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
        $sql = "UPDATE `$table` SET\n$fields\nWHERE $where";
        $stmt = $this->prepare($sql);
        $stmt->execute($whereData);
        return $stmt->rowCount();
    }

    public function replace($table, $data, $or = null)
    {
        $this->prepareInsertOrReplace($table, $data, $or, 'REPLACE')->execute($data);
        return $this->lastInsertId();
    }

    public function prepareInsertOrReplace($table, $data, $or = null, $type = "INSERT")
    {
        if ($or) $or = " OR $or";
        $sql = vsprintf("$type$or INTO `$table` (\n\t%s\n)\nVALUES (\n\t:%s\n)\n", $this->buildFieldValues($data));
        return $this->prepare($sql);
    }

    // TABLE TOOLS METHODS ============================================================================
    // migrar estes métodos para sua própria classe

    public function dropTable($name)
    {
        $this->exec("DROP TABLE IF EXISTS `$name`");
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
        $sql = "CREATE TABLE IF NOT EXISTS `$name` (\n$fields\n)";
        $this->exec($sql);
        return $this;
    }

    public function createIndex($table, $column)
    {
        return $this->exec("CREATE INDEX `$column` ON `$table` (`$column`)");
    }

    public function dropIndex($table, $column)
    {
        return $this->exec("DROP INDEX `$column` ON `$table`");
    }

    public function exec($statement)
    {
        $this->queryHistory[] = $this->lastQuery = $statement;
        try {
            return parent::exec($statement);
        }catch(PDOException $e) {
            throw new ExPdo($statement, $e);
        }
    }

    public function query($statement, $fetch = null, $a = null, $b = null, $c = null)
    {
        $this->queryHistory[] = $this->lastQuery = $statement;
        try {
            return parent::query($statement, $fetch, $a, $b, $c);
        }catch (PDOException $e) {
            throw new ExPdo($statement, $e);
        }
    }
}
