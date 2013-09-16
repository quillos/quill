<?php namespace Quill\Auth\Drivers;

use Quill\Arr;
use Quill\Hash;
use Quill\Config;
use Quill\Database\Query;

class Fluent extends Driver {
    public function retrieve($id)
    {
        if (filter_var($id, FILTER_VALIDATE_INT) !== false)
        {
            return Query::table(Config::get('auth.table'))->where('id','=',$id)->fetch();
        }
    }
    public function attempt($arguments = array())
    {
        $user = $this->get_user($arguments);
        $password = $arguments['pass'];
        $password_field = Config::get('auth.password', 'password');
        if ( ! is_null($user) and Hash::check($password, $user->{$password_field}))
        {
            return $this->login($user->id, Arr::get($arguments, 'remember'));
        }
        return false;
    }
    protected function get_user($arguments)
    {
        $table = Config::get('auth.table');
        $username = Config::get('auth.username');
        return Query::table($table)->where($username, '=', $arguments['login'])->fetch();
    }
}