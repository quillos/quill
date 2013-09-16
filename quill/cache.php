<?php namespace Quill; 

use Closure;
use Exception;

class Cache
{
    public static $drivers = array();
    public static $registrar = array();
    public static function driver($driver = null)
    {
        if (is_null($driver)) $driver = Config::get('cache.driver');

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
            case 'apc':
                return new Cache\Drivers\APC(Config::get('cache.key'));
            //case 'memcached':
            //    return new Cache\Drivers\Memcached(Memcached::connection(), Config::get('cache.key'));
            default:
                throw new Exception("Cache driver {$driver} is not supported.");
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
