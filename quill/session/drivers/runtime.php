<?php namespace Quill\Session\Drivers; 

class Runtime implements Driver
{
    public function __construct()
    {
        if (!session_id()) session_start();
    }
    public function end()
    {
        $_SESSION = array();
        if (isset($_COOKIE[session_name()]))
        {
            setcookie(session_name(), '', time()-42000, '/');
        }
        session_destroy();
    }
    public function get($key = null)
    {
        if(empty($key) || !isset($_SESSION[$key]))
            return;
        return $_SESSION[$key];
    }
    public function set($key = null, $value = null)
    {
        if(empty($key))
            return false;
        $_SESSION[$key] = $value;
        return $value;
    }
    public function delete($key)
    {
        if(isset($_SESSION[$key]))
        {
            unset($_SESSION[$key]);
            return true;
        }
        return false;
    }
}