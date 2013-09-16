<?php

class Driver
{
  const MEMCACHED = 'EpiCache_Memcached';
  const APC = 'EpiCache_Apc';
  private static $instances, $employ;
  private $cached, $hash;
  private function __construct(){}
  
  public static function getInstance()
  {
    $params = func_get_args();
    $hash   = md5(implode('.', $params));
    if(isset(self::$instances[$hash]))
      return self::$instances[$hash];
    $type = array_shift($params);
    if(!file_exists($file = dirname(__FILE__) . "/{$type}.php"))
      EpiException::raise(EpiCacheTypeDoesNotExistException("EpiCache type does not exist: ({$type}).  Tried loading {$file}", 404));

    require_once $file;
    self::$instances[$hash] = new $type($params);
    self::$instances[$hash]->hash = $hash;
    return self::$instances[$hash];
  }
  protected function getEpiCache($key)
  {
    if(isset($this->cached[$this->hash][$key]))
      return $this->cached[$this->hash][$key];
    else
      return false;
  }
  protected function setEpiCache($key, $value)
  {
    $this->cached[$this->hash][$key] = $value;
  }
  protected function getByKey()
  {
    $params = func_get_args();
    return $this->get(implode('.', $params));
  }
  protected function setByKey()
  {
    $params = func_get_args();
    $value = array_pop($params);
    return $this->set(implode('.', $params), $value);
  }
  public static function employ()
  {
    if(func_num_args() === 1)
      self::$employ = func_get_arg(0);

    return self::$employ;
  }
}