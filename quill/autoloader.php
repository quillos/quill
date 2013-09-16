<?php namespace Quill;

class Autoloader
{
    public static $aliases = array();
    public static $paths = array();
    public static function register()
    {
        spl_autoload_register(array(__CLASS__, 'load'));
    }
    public static function load($class) {
        if(array_key_exists(strtolower($class), array_change_key_case(static::$aliases))) {
            return class_alias(static::$aliases[$class], $class);
        }
        $lower = strtolower($class);
        $lower = explode('\\', $lower);
        $name = array_pop($lower);
        if (preg_match('/(\w+)(node\b|expression\b)/i', $name, $matches) )
        {
            $name = $matches[2].'/'.$matches[1];
        }
        if(is_readable($path = PATH . implode('/', $lower) . DS . $name . EXT))
        {
            return require_once $path;
        }
        return false;
    }
}

