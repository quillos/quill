<?php namespace Quill;

class ORM
{
    public static function factory($class)
    {
        $class = ucfirst($class);
        $class = "\\Quill\\Model\\$class";
        return new $class();
    }
}