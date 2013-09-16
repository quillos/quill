<?php namespace Quill;

use Exception;
use Quill\Template\Loader;

class View
{
    public static $loader = null;
    public static $vars = array();
    public $from;
    public static function __callStatic($module, $arguments)
    {
        try {
            $template = static::loader()->load(static::getPath($module) . array_shift($arguments));
            $vars = array_shift($arguments);
            if (!is_array($vars))
                $vars = array($vars);
            $vars = array_merge(static::$vars, $vars);
            $template->display($vars);
        } catch (Exception $e) {
            throw new Exception("Error Processing Request {$e->getMessage()}", 1);
        }
    }
    public static function module($name = 'site') {
        return new static(getPath($name));
    }
    public static function loader()
    {
        if (is_null(static::$loader))
        {
            static::$loader = new Loader(array(
                'mode' => 0,
                'source' => THEME.'default/views',
                'target' => APP.'cache/templates',
            ));
        }
        return static::$loader;
    }
    public static function getPath($module)
    {
        switch ($module) {
            case 'site':
                $from = THEME.'default/views/';
                break;
            case 'admin':
                $from = APP.'views/';
                break;
            default:
                $from = MODULE . $module . '/views/';
                break;
        }
        return $from;
    }
    public static function addGlobal($tag, $value)
    {
        static::$vars[$tag] = $value;
    }
    public function __construct($from = null)
    {
        $this->from = $from;
    }
    public function render($file, $vars = array())
    {
        $vars = array_merge(static::$vars, $vars);
        try {
            $template = static::loader()->load($this->from.$file);
            $template->display($vars);
        } catch (Exception $e) {
            throw new Exception("Error Processing Request {$e->getMessage()}", 1);
        }
    }
}