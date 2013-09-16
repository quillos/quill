<?php namespace Quill\Database\Connector;

use PDO;
use PDOException;
use ErrorException;
use Exception;

abstract class Connector
{
    protected $pdo;
    public $table_prefix = '';
    private $queries = array();

    public function __construct($config)
    {
        try
        {
            $this->pdo = $this->connect($config);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if(isset($config['prefix']))
            {
                $this->table_prefix = $config['prefix'];
            }
        }
        catch(PDOException $e)
        {
            throw new ErrorException($e->getMessage());
        }
    }
    abstract protected function connect($config);
    public function query($sql, $bindings = array())
    {
        try
        {
            $profile = true;
            if($profile)
            {
                $this->queries[] = compact('sql', 'binds');
            }
            $statement = $this->pdo->prepare($sql);
            $result = $statement->execute($bindings);
            return array($result, $statement);
        }
        catch(PDOException $e)
        {
            throw new Exception($sql, $e);
        }
    }
    public function profile()
    {
        return $this->queries;
    }
    public function instance()
    {
        return $this->pdo;
    }
    public static function __callStatic($method, $arguments)
    {
        return call_user_func_array(array($this->pdo, $method), $arguments);
    }
}