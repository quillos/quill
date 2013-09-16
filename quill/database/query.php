<?php namespace Quill\Database;

use PDO;
use Closure;
use Quill\Database;

class Query extends Builder {
    public $table;
    public $connection;
    public $fetch_class = 'StdClass';
    public $columns = array();
    public $join = array();
    public $where = array();
    public $sortby = array();
    public $groupby = array();
    public $limit;
    public $offset;
    public $bind = array();
    public static function table($table, $connection = null) {
        return new static($table, $connection);
    }
    public function __construct($table, $connection = null) {
        if(is_null($connection)) $connection = Database::connection();
        $this->table = $table;
        $this->connection = $connection;
    }
    public function apply($class) {
        $this->fetch_class = $class;
        return $this;
    }
    public function count() {
        list($result, $statement) = $this->connection->query($this->build_select_count(), $this->bind);
        return $statement->fetchColumn();
    }
    public function column($columns = array(), $column_number = 0) {
        list($result, $statement) = $this->connection->query($this->build_select($columns), $this->bind);
        return $statement->fetchColumn($column_number);
    }
    public function fetch($columns = null) {
        list($result, $statement) = $this->connection->query($this->build_select($columns), $this->bind);
        $statement->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, $this->fetch_class);
        return $statement->fetch();
    }
    public function get($columns = null) {
        list($result, $statement) = $this->connection->query($this->build_select($columns), $this->bind);
        $statement->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, $this->fetch_class);
        return $statement->fetchAll();
    }
    public function insert($row) {
        list($result, $statement) = $this->connection->query($this->build_insert($row), $this->bind);
        return $statement->rowCount();
    }
    public function insert_get_id($row) {
        list($result, $statement) = $this->connection->query($this->build_insert($row), $this->bind);
        return $this->connection->instance()->lastInsertId();
    }
    public function update($row) {
        list($result, $statement) = $this->connection->query($this->build_update($row), $this->bind);
        return $statement->rowCount();
    }
    public function delete() {
        list($result, $statement) = $this->connection->query($this->build_delete(), $this->bind);
        return $statement->rowCount();
    }
    public function where($column, $operator, $value) {
        $this->where[] = (count($this->where) ? 'AND ' : 'WHERE ') .
            $this->wrap_column($column) . ' ' . $operator . ' ?';
        $this->bind[] = $value;
        return $this;
    }
    public function or_where($column, $operator, $value) {
        $this->where[] = (count($this->where) ? 'OR ' : 'WHERE ') .
            $this->wrap_column($column) . ' ' . $operator . ' ?';
        $this->bind[] = $value;
        return $this;
    }
    public function where_in($column, $values) {
        $this->where[] = (count($this->where) ? 'OR ' : 'WHERE ') .
            $this->wrap_column($column) . ' IN (' . $this->placeholders(count($values)) . ')';
        $this->bind = array_merge($this->bind, $values);
        return $this;
    }
    public function join($table, $left, $operator, $right, $type = 'INNER') {
        if($table instanceof Closure) {
            list($query, $alias) = $table();
            $this->bind = array_merge($this->bind, $query->bind);
            $table = '(' . $query->build_select() . ') AS ' . $this->wrap_column($alias);
        }
        else $table = $this->wrap_table($table);
        $this->join[] = sprintf('%s JOIN %s ON (%s %s %s)',
            $type, $table, $this->wrap_column($left), $operator, $this->wrap_column($right));
        return $this;
    }
    public function left_join($table, $left, $operator, $right) {
        return $this->join($table, $left, $operator, $right, 'LEFT');
    }
    public function sort($column, $mode = 'ASC') {
        $this->sortby[] = $this->wrap_column($column) . ' ' . strtoupper($mode);
        return $this;
    }
    public function group($column) {
        $this->groupby[] = $this->wrap_column($column);
        return $this;
    }
    public function take($num) {
        $this->limit = $num;
        return $this;
    }
    public function skip($num) {
        $this->offset = $num;
        return $this;
    }
}