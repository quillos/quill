<?php namespace Quill;

use Quill\Database\Query;

abstract class Model
{
    protected static $cache = array();
    protected $data = array();
    public static $table;
    public static $primary = 'id';

    public function __construct($row = array())
    {
        $this->data = $row;
    }
    public function __call($method, $arguments)
    {
        echo $this->$method;
    }
    public function save()
    {
        if(isset($this->data[static::$primary]))
        {
            return static::where(static::$primary, '=', $this->data[static::$primary])->update($this->data);
        }
        return static::insert($this->data);
    }
    public function delete()
    {
        static::where(static::$primary, '=', $this->data[static::$primary])->delete();
    }
    public function populate($row)
    {
        $this->data = array_merge($this->data, (is_object($row) ? get_object_vars($row) : $row));
    }
    public function __get($key)
    {
        if(array_key_exists($key, $this->data))
        {
            return $this->data[$key];
        }
    }
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }
    public static function find($id)
    {
        $class = get_called_class();
        $key = $class . $id;
        if(isset(static::$cache[$key])) return static::$cache[$key];
        $result = static::where(static::$primary, '=', $id)->apply($class)->fetch();
        if($result) static::$cache[$key] = $result;
        return $result;
    }
    public static function create($row)
    {
        return static::find(static::insert_get_id($row));
    }
    public static function update($id, $row)
    {
        return static::where(static::$primary, '=', $id)->update($row);
    }
    public static function __callStatic($method, $arguments)
    {
        $obj = Query::table(static::$table)->apply(get_called_class());
        if(method_exists($obj, $method))
        {
            return call_user_func_array(array($obj, $method), $arguments);
        }
    }
}