<?php namespace Quill; 

use Closure;
use Exception;

class Auth
{
    public static $drivers = array();
    public static $registrar = array();
    public static function driver($driver = null)
    {
        if (is_null($driver)) $driver = Config::get('auth.driver');
        if ( ! isset(static::$drivers[$driver]))
        {
            static::$drivers[$driver] = static::factory($driver);
        }
        return static::$drivers[$driver];
    }
    protected static function factory($driver)
    {
        if (isset(static::$registrar[$driver]))
        {
            $resolver = static::$registrar[$driver];
            return $resolver();
        }
        switch ($driver)
        {
            case 'fluent':
                return new Auth\Drivers\Fluent(Config::get('auth.table'));
            default:
                throw new Exception("Auth driver {$driver} is not supported.");
        }
    }
    public static function extend($driver, Closure $resolver)
    {
        static::$registrar[$driver] = $resolver;
    }
    public static function __callStatic($method, $parameters)
    {
        return call_user_func_array(array(static::driver(), $method), $parameters);
    }
}