<?php namespace Quill;

class Arr
{
    public static function get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }
        if (is_array($key)) {
            $result = array();
            foreach ($key as $k) {
                $result[$k] = static::get($array, $k, $default);
            }
            return $result;
        }
        foreach($keys = explode('.', $key) as $key)
        {
            if( ! is_array($array) or ! array_key_exists($key, $array))
            {
                return $default;
            }
            $array =& $array[$key];
        }
        return $array;
    }
    public static function set(&$array, $key, $value = null)
    {
        $keys = explode('.', $key);
        while(count($keys) > 1)
        {
            $key = array_shift($keys);
            if( ! array_key_exists($key, $array))
            {
                $array[$key] = array();
            }
            $array =& $array[$key];
        }
        $array[array_shift($keys)] = $value;
    }
    public static function delete(&$array, $key)
    {
        $keys = explode('.', $key);
        while(count($keys) > 1)
        {
            $key = array_shift($keys);

            if(array_key_exists($key, $array))
            {
                $array =& $array[$key];
            }
        }
        if(array_key_exists($key = array_shift($keys), $array))
        {
            unset($array[$key]);
        }
    }
    public static function create($stack = array())
    {
        return new static($stack);
    }
    public function __construct($stack = array())
    {
        $this->stack = $stack;
    }
    public function shuffle()
    {
        shuffle($this->stack);
        return $this;
    }
    public function first()
    {
        return current($this->stack);
    }
    public function last()
    {
        return end($this->stack);
    }
}