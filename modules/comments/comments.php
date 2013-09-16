<?php

Filter::add('comment_content', 'wpautop');

UI::add('comments', function( $pid = 0 ) {
    $vars['comments'] = Comment::getByPostId($pid);
    return View::comments('comments.html', $vars);
});