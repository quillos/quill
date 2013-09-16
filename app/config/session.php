<?php

return array(
    'driver' => 'runtime',
    'table' => 'sessions',
    'sweepage' => array(2, 100),
    'lifetime' => 60,
    'expire_on_close' => false,
    'cookie' => 'laravel_session',
    'path' => '/',
    'domain' => null,
    'secure' => false,
);