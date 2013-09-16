<?php namespace Quill\Auth\Drivers;

use Quill\Cookie;
use Quill\Config;
use Quill\Session;
use Quill\Crypter;
use Quill\Str;

abstract class Driver {
    public $user;
    public $token;
    public function __construct()
    {
        if (Session::started())
        {
            $this->token = Session::get($this->token());
        }
        if (is_null($this->token))
        {
            $this->token = $this->recall();
        }
    }
    public function guest()
    {
        return ! $this->check();
    }
    public function check()
    {
        return ! is_null($this->user());
    }
    public function user()
    {
        if ( ! is_null($this->user)) return $this->user;
        return $this->user = $this->retrieve($this->token);
    }
    abstract public function retrieve($id);
    abstract public function attempt($arguments = array());
    public function login($token, $remember = false)
    {
        $this->token = $token;
        $this->store($token);
        if ($remember) $this->remember($token);
        return true;
    }
    public function logout()
    {
        $this->user = null;
        $this->cookie($this->recaller(), null, -2000);
        Session::delete($this->token());
        $this->token = null;
    }
    protected function store($token)
    {
        Session::set($this->token(), $token);
    }
    protected function remember($token)
    {

        $token = Crypter::encrypt($token.'|'.Str::random(40));
        echo $this->recaller();
        $this->cookie($this->recaller(), $token, Cookie::forever);
    }
    public function recall()
    {
        $cookie = Cookie::get($this->recaller());
        if ( ! is_null($cookie))
        {
            $token = explode('|', Crypter::decrypt($cookie));
            return $token[0];
        }
    }
    protected function cookie($name, $value, $minutes)
    {
        $config = Config::get('session');
        extract($config);
        Cookie::set($name, $value, $minutes, $path, $domain, $secure);
    }
    protected function token()
    {
        return $this->name().'_login';
    }
    protected function recaller()
    {
        return Config::get('auth.cookie', $this->name().'_remember');
    }
    protected function name()
    {
        return strtolower(str_replace('\\', '_', get_class($this)));
    }
}
