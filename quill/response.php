<?php namespace Quill;

class Response
{
    public static function error($code='404')
    {
        echo View::site($code.'.html');
    }
}