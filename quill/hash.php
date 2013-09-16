<?php namespace Quill;

class Hash {
    public static function create($value, $rounds = 8)
    {
        $work = str_pad($rounds, 2, '0', STR_PAD_LEFT);
        if (function_exists('openssl_random_pseudo_bytes'))
        {
            $salt = openssl_random_pseudo_bytes(16);
        }
        else
        {
            $salt = Str::random(40);
        }
        $salt = substr(strtr(base64_encode($salt), '+', '.'), 0 , 22);
        return crypt($value, '$2a$'.$work.'$'.$salt);
    }
    public static function check($value, $hash)
    {
        return crypt($value, $hash) === $hash;
    }
}