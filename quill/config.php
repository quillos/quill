<?php namespace Quill;

class Config
{
    public static $array = array();
    public static function get($key, $default = null)
    {
        $keys = explode('.', $key);
        if( ! array_key_exists($file = current($keys), static::$array))
        {
            if(constant('ENV') and is_readable($path = APP . 'config' . DS . ENV . DS . $file . EXT))
            {
                static::$array[$file] = require $path;
            }
            elseif(is_readable($path = APP . 'config' . DS . $file . EXT))
            {
                static::$array[$file] = require $path;
            }
        }
        return Arr::get(static::$array, $key, $default);
    }
    public static function set($key, $value)
    {
        Arr::set(static::$array, $key, $value);
    }
    public static function delete($key)
    {
        Arr::erase(static::$array, $key);
    }
    public static function __callStatic($method, $arguments)
    {
        $key = $method;
        $fallback = null;
        if(count($arguments))
        {
            $key .= '.' . array_shift($arguments);
            $fallback = array_shift($arguments);
        }
        return static::get($key, $fallback);
    }
}