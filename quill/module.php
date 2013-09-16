<?php namespace Quill;

class Module
{
    public static $modules;
    public static function add($modules)
    {
        static::addAdmin();
        $modules = func_get_args();
        foreach ($modules as $module)
        {
            $path = MODULE . $module . DS;
            require_path( $path . 'libs' . DS );
            require_path( $path . 'models' . DS );
            require_path( $path . 'routes' . DS );
            require_i18n( $path . 'i18n' . DS );
            require_file( $path . $module . EXT );
        }
        static::addSite();
    }

    public static function addAdmin()
    {
        $path = APP;
        require_path( $path . 'libs' . DS );
        require_path( $path . 'models' . DS );
        require_path( $path . 'routes' . DS );
        require_i18n( $path . 'i18n' . DS );
    }
    public static function addSite($theme='default')
    {
        $path = THEME . $theme . DS;
        require_path( $path . 'libs' . DS );
        require_i18n( $path . 'i18n' . DS );
    }
}