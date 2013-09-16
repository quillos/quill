<?php namespace Quill;

class Cookie
{
    const forever = 2628000;
    public static function set($name, $value, $expiration = 0, $path = '/', $domain = null, $secure = false)
    {
        return setcookie($name, $value, time() + $expiration, $path, $domain, $secure);
    }
    public static function forever($name, $value, $path = '/', $domain = null, $secure = false)
    {
        return static::set($name, $value, static::forever, $path, $domain, $secure);
    }
    public static function get($name = null, $default = null)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
    }
    public static function delete($name, $path = '/', $domain = null, $secure = false)
    {
        return static::set($name, null, -86400, $path, $domain, $secure);
    }
    public static function flush()
    {
        return $_COOKIE;
    }
    public static function destory()
    {
        $_COOKIE = array();
    }
}
