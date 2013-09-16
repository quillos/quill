<?php namespace Quill;

Module::add('comments');

Filter::add('post_content', 'wpautop');

View::addGlobal('blog', Config::get('app'));

//var_dump(Auth::user());

View::addGlobal('current_user', Auth::user());

