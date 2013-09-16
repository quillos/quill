<?php namespace Quill;

use Exception;

class Str
{
    public static function random($length, $type = 'alnum')
    {
        return substr(str_shuffle(str_repeat(static::pool($type), 5)), 0, $length);
    }
    protected static function pool($type)
    {
        switch ($type)
        {
            case 'alpha':
                return 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            case 'alnum':
                return '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            default:
                throw new Exception("Invalid random string type [$type].");
        }
    }
}