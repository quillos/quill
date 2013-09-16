<?php

return array(
    'driver' => 'apc',
    'key' => 'laravel',
    'database' => array('table' => 'laravel_cache'),
    'memcached' => array(
        array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 100),
    ),    
);