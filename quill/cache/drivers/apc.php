<?php namespace Quill\Cache\Drivers;

class APC extends Driver {
    protected $key;
    public function __construct($key)
    {
        $this->key = $key;
    }
    public function has($key)
    {
        return ( ! is_null($this->get($key)));
    }
    protected function retrieve($key)
    {
        if (($cache = apc_fetch($this->key.$key)) !== false)
        {
            return $cache;
        }
    }
    public function put($key, $value, $minutes)
    {
        apc_store($this->key.$key, $value, $minutes * 60);
    }
    public function forever($key, $value)
    {
        return $this->put($key, $value, 0);
    }
    public function forget($key)
    {
        apc_delete($this->key.$key);
    }
}