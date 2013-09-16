<?php

Filter::add('post_content', function($text) {
	$texts = explode('<!--nextpage-->', $text);
	$text = $texts[0];
	if ($count = count($texts) > 1) {
		$url = $_SERVER['REQUEST_URI'];
		$text .= '<ul class="nextpage">';
		for ($i=1; $i <= count($texts); $i++) { 
			$text .= sprintf('<a href="%s/%s">%s</a>', $url, $i, $i);
		}
		$text .= '</ul>';
	}
	return $text;
}, 1);

Filter::add('author_avatar', function($avatar) {
    $url = Config::app('url');
    $t = 604800;
    if( preg_match('#src="(.+)"\s+#', $avatar, $matches) )
    {
        $avatar = $matches[1];
    }
    if( preg_match('#/avatar/(\w+)\?\w+#', $avatar, $matches) )
    {
        $name = $matches[1];
        $file = APP . 'cache/avatar/'. $name . '.png';
        if( ! file_exists($file) or time() - fileatime($file) > $t )
        {
            copy($avatar, $file);
        }
        $avatar = $url . '/app/cache/avatar/' . $name . '.png';
    }
    return $avatar;
});