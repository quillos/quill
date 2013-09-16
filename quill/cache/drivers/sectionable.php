<?php namespace Quill\Cache\Drivers;

abstract class Sectionable extends Driver {
    public $implicit = true;
    public $delimiter = '::';
    public function get_from_section($section, $key, $default = null)
    {
        return $this->get($this->section_item_key($section, $key), $default);
    }
    public function put_in_section($section, $key, $value, $minutes)
    {
        $this->put($this->section_item_key($section, $key), $value, $minutes);
    }
    public function forever_in_section($section, $key, $value)
    {
        return $this->forever($this->section_item_key($section, $key), $value);
    }
    public function remember_in_section($section, $key, $default, $minutes, $function = 'put')
    {
        $key = $this->section_item_key($section, $key);

        return $this->remember($key, $default, $minutes, $function);
    }
    public function sear_in_section($section, $key, $default)
    {
        return $this->sear($this->section_item_key($section, $key), $default);
    }
    public function forget_in_section($section, $key)
    {
        return $this->forget($this->section_item_key($section, $key));
    }
    abstract public function forget_section($section);
    protected function sectionable($key)
    {
        return $this->implicit and $this->sectioned($key);
    }
    protected function sectioned($key)
    {
        return str_contains($key, '::');
    }
    protected function parse($key)
    {
        return explode('::', $key, 2);
    }
}