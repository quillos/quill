<?php namespace Quill;

class Input
{
    public static function get($index = null, $default = null) {
        return (func_num_args() === 0) ? $_GET : Arr::get($_GET, $index, $default);
    }
    public static function post($index = null, $default = null) {
        return (func_num_args() === 0) ? $_POST : Arr::get($_POST, $index, $default);
    }
}