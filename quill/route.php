<?php namespace Quill;

class Route
{
    protected static $config;
    protected static $routes = array();
    protected static $filters = array();
    protected static $request;
    protected static $response;
    private function __construct() {}
    public static function init() {}
    public static function before() {
        $args = func_get_args();
        return static::mapFilter($args, "before");
    }
    public static function after() {
        $args = func_get_args();
        return static::mapFilter($args, "after");
    }
    public static function __callStatic($method, $arguments) {
        return static::mapRoute($arguments, strtoupper($method));
    }
    private static function router($url = null, $method = null) {
        $url = array(
            'original' => $url,
            'path' => explode('/', parse_url($url, PHP_URL_PATH))
        );
        $url['length'] = count($url['path']);
        foreach (static::$routes[$method] as $pattern => $data) {
            $parameters = array();
            $pattern = array (
                'original' => $pattern,
                'path' => explode('/', $pattern)
            );
            $pattern['length'] = count($pattern['path']);
            if ($url['length'] <> $pattern['length']) {
                continue;
            }
            foreach($pattern['path'] as $i => $key) {
                if (strpos($key, ':') === 0) {
                    $parameters[substr($key, 1)] = $url['path'][$i];
                } else if($key != $url['path'][$i]) {
                    continue 2;
                }
            }
            if ( ! array_key_exists('parameters', $data)) {
                $data['parameters'] = array();
            }
            $data['parameters'] = array_merge($data['parameters'], $parameters);
            return $data;
        }
        return false;
    }
    
    private static function filter($name = null) {
        if (array_key_exists($name, static::$filters)) {
            return static::$filters[$name];
        }
        return false;
    }
    private static function mapRoute($args = array(), $method = null) {
        $pattern = array_shift($args);
        $callback = array_pop($args);
        $filter = array_shift($args);
        static::$routes[$method][$pattern] = array(
            "method" => $method, 
            "callback" => $callback, 
            "filter" => $filter
        );
    }
    private static function mapFilter($args = array(), $position = null) {
        $filter = array_shift($args);
        $callback = array_pop($args);
        static::$filters[$filter] = array(
            'callback' => $callback,
            'position' => $position
        );
    }
    public static function run()
    {
        $pattern = ltrim($_SERVER['REQUEST_URI'], "/");
        $pattern = empty($pattern) ? '/' : $pattern;
        $method = $_SERVER['REQUEST_METHOD'];
        $route = static::router($pattern, $method);
        if( ! $route)
        {
            return Response::error(404);
        }
        $filter = null;
        if ( ! is_null($route['filter']))
        {
            $filter = static::filter($route['filter']);
        }
        if ($filter['position'] == "before")
        {
            call_user_func($filter['callback']);
        }
        call_user_func_array($route['callback'], $route['parameters']);
        if ($filter['position'] == "after")
        {
            call_user_func($filter['callback']);
        }
    }
}