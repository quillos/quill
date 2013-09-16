<?php

Route::before('auth', function()
{
    if(Auth::check()) Redirect::to('admin');
});

Route::get('login', 'auth', function()
{
    return View::admin('login.html');
});

Route::post('login', function()
{
    $user = Input::post(array('login', 'pass', 'remember'));
    if (!Auth::attempt($user))
    {
        $vars['message'] = 'Login Error!';
        $vars['user'] = $user;
        return View::admin('login.html', $vars);
    }
    Redirect::to('admin');
});

Route::get('logout', function()
{
    Auth::logout();
    Redirect::to('/');
});