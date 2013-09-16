<?php namespace Quill\Database\Connector;

use PDO;

class Sqlite extends Connector
{
    public $wrapper = '[%s]';
    protected function connect($config)
    {
        extract($config);
        $dns = 'sqlite:' . $database;
        return new PDO($dns);
    }
}