<?php namespace Quill;

class UI
{
	private static $modules = null;
	public static function apply($module, $args)
	{
	    if ( is_null(static::$modules) or isset(static::$modules[$module]))
	    {
	        return call_user_func_array(static::$modules[$module], $args);
	    }
	}
	public static function add($module, $function)
	{
	    static::$modules[$module] = $function;
	}
}