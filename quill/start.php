<?php namespace Quill;

date_default_timezone_set('PRC');

$start = microtime(true);

require_once QUILL . 'autoloader' . EXT;

Autoloader::register();

Autoloader::$aliases = Config::get('aliases', array());

Error::register();

require_once QUILL . 'helpers' . EXT;

require_once QUILL . 'functions' . EXT;

require_once APP . 'bootstrap' . EXT;

$end = round((microtime(true) - $start), 5);

Session::load();

Route::run();