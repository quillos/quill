<?php namespace Quill;

use ErrorException;
use Quill\Database\Connector\Mysql;
use Quill\Database\Connector\Sqlite;

class Database
{
    public static $connections = array();
    public static function factory($config)
    {
        switch($config['driver'])
        {
            case 'mysql':
                return new Mysql($config);
            case 'sqlite':
                return new Sqlite($config);
        }
        throw new ErrorException('Unknown database driver');
    }
    public static function connection($connection = null)
    {
        if(is_null($connection)) $connection = 'mysql';
        if(isset(static::$connections[$connection])) return static::$connections[$connection];
        return (static::$connections[$connection] = static::factory(Config::get('database')));
    }
    public static function profile($name = null)
    {
        return static::connection($name)->profile();
    }
    public static function __callStatic($method, $parameters)
    {
        return call_user_func_array(array(static::connection(), $method), $parameters);
    }
}