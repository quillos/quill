<?php namespace Quill\Cache\Drivers;

class Memcached extends Sectionable {

    public $memcache;
    protected $key;
    public function __construct(\Memcached $memcache, $key)
    {
        $this->key = $key;
        $this->memcache = $memcache;
    }
    public function has($key)
    {
        return ( ! is_null($this->get($key)));
    }
    protected function retrieve($key)
    {
        if ($this->sectionable($key))
        {
            list($section, $key) = $this->parse($key);
            return $this->get_from_section($section, $key);
        }
        elseif (($cache = $this->memcache->get($this->key.$key)) !== false)
        {
            return $cache;
        }
    }
    public function put($key, $value, $minutes)
    {
        if ($this->sectionable($key))
        {
            list($section, $key) = $this->parse($key);
            return $this->put_in_section($section, $key, $value, $minutes);
        }
        else
        {
            $this->memcache->set($this->key.$key, $value, $minutes * 60);
        }
    }
    public function forever($key, $value)
    {
        if ($this->sectionable($key))
        {
            list($section, $key) = $this->parse($key);
            return $this->forever_in_section($section, $key, $value);
        }
        else
        {
            return $this->put($key, $value, 0);
        }
    }
    public function forget($key)
    {
        if ($this->sectionable($key))
        {
            list($section, $key) = $this->parse($key);
            if ($key == '*')
            {
                $this->forget_section($section);
            }
            else
            {
                $this->forget_in_section($section, $key);
            }
        }
        else
        {
            $this->memcache->delete($this->key.$key);
        }
    }
    public function forget_section($section)
    {
        return $this->memcache->increment($this->key.$this->section_key($section));
    }
    protected function section_id($section)
    {
        return $this->sear($this->section_key($section), function()
        {
            return rand(1, 10000);
        });
    }
    protected function section_key($section)
    {
        return $section.'_section_key';
    }
    protected function section_item_key($section, $key)
    {
        return $section.'#'.$this->section_id($section).'#'.$key;
    }
}