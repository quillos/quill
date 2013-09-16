<?php

Route::get('/home',function() {
    \Quill\Template::render();
});

Route::get('/', function()
{
    $perpage = 5;
    $page = 1;
    $vars['posts'] = Post::where('post_type','=','post')
        ->where('post_status','=','publish')
        ->sort('post_date','desc')
        ->take($perpage)
        ->skip(($page - 1) * $perpage)
        ->get();
    return View::site('index.html', $vars);
});

Route::get('page/:page', function($page = 1)
{
    $perpage = 5;
    //$page = 1;
    $vars['posts'] = Post::where('post_type','=','post')
        ->where('post_status','=','publish')
        ->sort('post_date','desc')
        ->take($perpage)
        ->skip(($page - 1) * $perpage)
        ->get();
    return View::site('index.html', $vars);
});

Route::get('post/:id', function($id)
{
    $vars['post'] = Post::where('ID','=',$id)->fetch();
    return View::site('post.html', $vars);
});