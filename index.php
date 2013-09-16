<?php

define('START_TIME', microtime(true));
define('START_MEMORY', memory_get_usage(true));

define('DS', DIRECTORY_SEPARATOR);
define('ENV', getenv('APP_ENV'));
define('PATH', __DIR__.DS);   // dirname(__FILE__)
define('QUILL', PATH.'quill'.DS);
define('APP', PATH.'app'.DS);
define('THEME', PATH.'themes'.DS);
define('MODULE', PATH.'modules'.DS);
define('EXT', '.php');

require_once QUILL.'start'.EXT;