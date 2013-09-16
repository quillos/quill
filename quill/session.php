<?php namespace Quill; 

use Exception;

class Session
{
    public static $instance;
    const COOKIE = '__Session__';
    public static function load()
    {
        static::start(Config::get('session.driver'));
    }
    public static function start($driver)
    {
        static::$instance = static::factory($driver);
    }
    public static function factory($driver)
    {
        switch ($driver)
        {
            case 'runtime':
                return new Session\Drivers\Runtime;
            case 'apc':
                return new Session\Drivers\APC;
            case 'memcached':
                return new Session\Drivers\Memcached;
            default:
                throw new Exception("Session driver [$driver] is not supported.");
        }
    }
    public static function instance()
    {
        if (static::started()) return static::$instance;
        throw new Exception("A driver must be set before using the session.");
    }
    public static function started()
    {
        return ! is_null(static::$instance);
    }
    public static function __callStatic($method, $parameters)
    {
        return call_user_func_array(array(static::instance(), $method), $parameters);
    }
}