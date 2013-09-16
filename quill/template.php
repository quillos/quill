<?php namespace Quill;

use Exception;
use Quill\Template\Loader;

class Template
{
    public function __construct($path = '')
    {}
    public static function render($file='', $vars = array())
    {
    	$source = array(PATH.'test/template', PATH);
    	$target = PATH.'test';
        $loader = new Loader(array(
        	'mode' => 1,
			'source' => $source[0],
			'target' => PATH.'test',
		));
		$template = $loader->load('post.html', '/srv/www/tiny/test/template/layout/');
		$template->display($vars);
    }
}