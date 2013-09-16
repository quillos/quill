<?php namespace Quill\Database\Connector;

use PDO;

class Mysql extends Connector
{
    public $wrapper = '`%s`';
    protected function connect($config)
    {
        extract($config);
        $dns = 'mysql:' . implode(';', array('dbname=' . $database, 'host=' . $hostname, 'port=' . $port, 'charset=' . $charset));
        return new PDO($dns, $username, $password);
    }
}