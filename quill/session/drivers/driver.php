<?php namespace Quill\Session\Drivers;

interface Driver {
    public function get($key = null);
    public function set($key = null, $value = null);
}