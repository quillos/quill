<?php
/*
class Grace
{
	public static $modules = array();
	public static $theme = null;
	public static function setup()
	{
		static::addModule('markdown', 'paging', 'avatar', 'comments');

	}
	public static addModule($module) {
		require_dir(MODPATH . 'modules' . $module . 'libs');
		require_dir(MODPATH . 'modules' . $module . 'models');
		require_dir(MODPATH . 'modules' . $module . 'routes');
		static::$modules[$module] = new $module;
	}
}*/

$module['comments'] = array(
	'name' => 'comments'
	'class' => new Comments;
	'paths' => array(
		'root' => '/srv/www/tiny/modules/comments',
		'libs' => '/srv/www/tiny/modules/comments/libs',
		'models' => '/srv/www/tiny/modules/comments/models',
		'views' => '/srv/www/tiny/modules/comments/views',
	),
)