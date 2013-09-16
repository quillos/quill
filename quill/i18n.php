<?php namespace Quill;

class I18n
{
    public static $lang = 'zh-cn';
    public static $paths = array();
    protected static $_cache = array();
    public static function lang($lang = NULL)
    {
        if ($lang)
        {
            I18n::$lang = strtolower(str_replace(array(' ', '_'), '-', $lang));
        }
        return I18n::$lang;
    }
    public static function get($string, $lang = NULL)
    {
        if ( ! $lang)
        {
            $lang = I18n::$lang;
        }
        $table = I18n::load($lang);
        return isset($table[$string]) ? $table[$string] : $string;
    }
    public static function load($lang)
    {
        if (isset(I18n::$_cache[$lang]))
        {
            return I18n::$_cache[$lang];
        }
        $table = array();
        $parts = explode('-', $lang);
        foreach (static::$paths as $path) {
            $file = $path . $lang . EXT;
            if(file_exists($file)) {
                $t = require $file;
                $table = array_merge($table, $t);
            }
        }
        return I18n::$_cache[$lang] = $table;
    }
}