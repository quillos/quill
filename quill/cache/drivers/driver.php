<?php namespace Quill\Cache\Drivers;

abstract class Driver {
    abstract public function has($key);
    public function get($key, $default = null)
    {
        return ( ! is_null($item = $this->retrieve($key))) ? $item : $default;
    }
    abstract protected function retrieve($key);
    abstract public function put($key, $value, $minutes);
    public function remember($key, $default, $minutes, $function = 'put')
    {
        if ( ! is_null($item = $this->get($key, null))) return $item;

        $this->$function($key, $default = $default, $minutes);

        return $default;
    }
    public function sear($key, $default)
    {
        return $this->remember($key, $default, null, 'forever');
    }
    abstract public function forget($key);
    protected function expiration($minutes)
    {
        return time() + ($minutes * 60);
    }
}
