<?php namespace Quill;

class Filter
{
	public static $filters = null;
	public static function add($tag, $function, $priority = 10)
	{
	    static::$filters[$tag][$priority][] = $function;
	}
	public static function apply($tag, $value)
	{
	    if (is_null(static::$filters)) return $value;
	    ksort(static::$filters[$tag]);
	    foreach (static::$filters[$tag] as $functions)
	    {
	        foreach ($functions as $function)
	        {
	            if(is_callable($function))
	            {
	                $value = call_user_func_array($function, array($value));
	            }
	            elseif(is_array($function) and is_callable($func = $function[1]))
	            {
	                $value = call_user_func($func, $value);
	            }
	            elseif(is_string($function) and is_callable('\\Quill\\'.$function))
	            {
	                $value = call_user_func('\\Quill\\'.$function, $value);
	            }
	        }
	    }
	    return $value;
	}

}