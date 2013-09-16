<?php

Route::get('admin', function() {
    return View::admin('dashboard.html');
});

Route::get('admin/post/new', function() {
    return View::admin('compose.html');
});

Route::get('admin/post/edit/:id', function($id) {
    $vars['post'] = Post::where('ID','=',$id)->fetch();
    return View::admin('compose.html', $vars);
});

Route::get('admin/post/delete/:id', function($id) {
    Post::delete($id);
    return Redirect::to('posts.html');
});