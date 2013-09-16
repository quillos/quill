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