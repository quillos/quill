<?php namespace Quill;

class Crypter {

    public static $cipher = MCRYPT_RIJNDAEL_256;
    public static $mode = MCRYPT_MODE_CBC;
    public static $block = 32;
    public static function encrypt($value)
    {
        $iv = mcrypt_create_iv(static::iv_size(), static::randomizer());
        $value = static::pad($value);
        $value = mcrypt_encrypt(static::$cipher, static::key(), $value, static::$mode, $iv);
        return base64_encode($iv.$value);
    }
    public static function decrypt($value)
    {
        $value = base64_decode($value);
        $iv = substr($value, 0, static::iv_size());
        $value = substr($value, static::iv_size());
        $key = static::key();
        $value = mcrypt_decrypt(static::$cipher, $key, $value, static::$mode, $iv);
        return static::unpad($value);
    }
    public static function randomizer()
    {
        if (defined('MCRYPT_DEV_URANDOM'))
        {
            return MCRYPT_DEV_URANDOM;
        }
        elseif (defined('MCRYPT_DEV_RANDOM'))
        {
            return MCRYPT_DEV_RANDOM;
        }
        else
        {
            mt_srand();
            return MCRYPT_RAND;
        }
    }
    protected static function iv_size()
    {
        return mcrypt_get_iv_size(static::$cipher, static::$mode);
    }
    protected static function pad($value)
    {
        $pad = static::$block - (strlen($value) % static::$block);
        return $value .= str_repeat(chr($pad), $pad);
    }
    protected static function unpad($value)
    {
        $pad = ord(substr($value, -1));
        if ($pad and $pad <= static::$block)
        {
            if (preg_match('/'.chr($pad).'{'.$pad.'}$/', $value))
            {
                return substr($value, 0, strlen($value) - $pad);
            }
            else
            {
                throw new \Exception("Decryption error. Padding is invalid.");
            }
        }
        return $value;
    }
    protected static function key()
    {
        return Config::get('app.key');
    }
}
