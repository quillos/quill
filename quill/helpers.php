<?php namespace Quill;

use ArrayIterator;
use Traversable;
use Countable;

function require_file($file)
{
    if(!file_exists($file)) return;
    require_once $file;
}
function require_path($path)
{
    if(!file_exists($path)) return;
    foreach (glob($path.'*.php') as $file)
    {
        //echo $file . '<br>';
        require_once $file;
    }
}
function require_i18n($path)
{
    I18n::$paths[] = $path;
}
function abs($obj = null)
{
    return \abs(intval($obj));
}
function bytes($obj = null, $decimals = 1, $dec = '.', $sep = ',')
{
    $obj = max(0, intval($obj));
    $places = strlen($obj);
    if ($places <= 9 && $places >= 7) {
        $obj = \number_format($obj / 1048576, $decimals, $dec, $sep);
        return "$obj MB";
    } elseif ($places >= 10) {
        $obj = \number_format($obj / 1073741824, $decimals, $dec, $sep);
        return "$obj GB";
    } else {
        $obj = \number_format($obj / 1024, $decimals, $dec, $sep);
        return "$obj KB";
    }
}
function capitalize($obj)
{
    return ucfirst(strval($obj));
}
/*function date($obj = null, $format = 'Y-m-d')
{
    //return \date($format, $obj ? '' : time());
    return $obj;
}*/

function rdate($time){
    $time = strtotime(trim($time));
    $sec = time() - $time;
    switch(true) {
        case $sec < 3600:
            return round($sec/60) . __('seconds ago') ;
        case $sec < 86400:
            return round($sec/3600) . __('hours ago');
        case $sec < (86400 * 3):
            return round($time/(60*60*24)) == 1 
                ? __('yesterday') . date('H:i', $time) 
                : __('the day before yesterday') . date('H:i', $time);
        case $sec < (86400 * 7):
            return round($sec/86400) . __('days ago') . date('H:i', $time);
        case $sec < (86400 * 7 * 4):
            return round($sec/(86400*7)) . __('weeks ago');
        case $sec < 86400 * 365:
            return date(__('n月d日'), $time);
        default:
            return date(__('Y年n月d日'), $time);
    }
}
function dump($obj = null)
{
    echo '<pre>';
    print_r($obj);
    echo '</pre>';
}
function e($obj = null, $force = false)
{
    return escape($obj, $force);
}
function escape($obj = null, $force = false)
{
    return htmlspecialchars(strval($obj), ENT_QUOTES, 'UTF-8', $force);
}
function first($obj = null, $default = null)
{
    if (is_string($obj)) return strlen($obj) ? substr($obj, 0, 1) : $default;
    $obj = ($obj instanceof Traversable) ?
        iterator_to_array($obj) : (array) $obj;
    $keys = array_keys($obj);
    if (count($keys)) {
        return $obj[$keys[0]];
    }
    return $default;
}
function format($obj, $args)
{
    return call_user_func_array('sprintf', func_get_args());
}
function is_divisible_by($obj = null, $number = null)
{
    if (!isset($number)) return false;
    if (!is_numeric($obj) || !is_numeric($number)) return false;
    if ($number == 0) return false;
    return ($obj % $number == 0);
}

function is_empty($obj = null)
{
    if (is_null($obj)) {
        return true;
    } elseif (is_array($obj)) {
        return empty($obj);
    } elseif (is_string($obj)) {
        return strlen($obj) == 0;
    } elseif ($obj instanceof Countable) {
        return count($obj) ? false : true;
    } elseif ($obj instanceof Traversable) {
        return iterator_count($obj);
    } else {
        return false;
    }
}

function is_even($obj = null)
{
    if (is_scalar($obj) || is_null($obj)) {
        $obj = is_numeric($obj) ? intval($obj) : strlen($obj);
    } elseif (is_array($obj)) {
        $obj = count($obj);
    } elseif ($obj instanceof Traversable) {
        $obj = iterator_count($obj);
    } else {
        return false;
    }
    return \abs($obj % 2) == 0;
}

function is_odd($obj = null)
{
    if (is_scalar($obj) || is_null($obj)) {
        $obj = is_numeric($obj) ? intval($obj) : strlen($obj);
    } elseif (is_array($obj)) {
        $obj = count($obj);
    } elseif ($obj instanceof Traversable) {
        $obj = iterator_count($obj);
    } else {
        return false;
    }
    return \abs($obj % 2) == 1;
}

function join($obj = null, $glue = '')
{
    $obj = ($obj instanceof Traversable) ?
        iterator_to_array($obj) : (array) $obj;
    return \join($glue, $obj);
}

function json_encode($obj = null)
{
    return \json_encode($obj);
}

function keys($obj = null)
{
    if (is_array($obj)) {
        return array_keys($obj);
    } elseif ($obj instanceof Traversable) {
        return array_keys(iterator_to_array($obj));
    }
    return null;
}

function last($obj = null, $default = null)
{
    if (is_string($obj)) return strlen($obj) ? substr($obj, -1) : $default;
    $obj = ($obj instanceof Traversable) ?
        iterator_to_array($obj) : (array) $obj;
    $keys = array_keys($obj);
    if ($len = count($keys)) {
        return $obj[$keys[$len - 1]];
    }
    return $default;
}

function length($obj = null)
{
    if (is_string($obj)) {
        return strlen($obj);
    } elseif (is_array($obj) || ($obj instanceof Countable)) {
        return count($obj);
    } elseif ($obj instanceof Traversable) {
        return iterator_count($obj);
    } else {
        return 1;
    }
}

function lower($obj = null)
{
    return strtolower(strval($obj));
}

function nl2br($obj = null, $is_xhtml = false)
{
    return \nl2br(strval($obj), $is_xhtml);
}

function number_format($obj = null, $decimals = 0, $dec_point = '.',
    $thousands_sep = ',')
{
    return \number_format(strval($obj), $decimals, $dec_point, $thousands_sep);
}
function cycle($obj = null, $position)
{
    if (!is_array($obj) && !$obj instanceof ArrayAccess)
    {
        return $obj;
    }
    return $obj[$position % count($obj)];
}
function range($first = null, $second = null, $step = 1)
{
    return \range($first, $second, $step);
    //return new RangeIterator(intval($lower), intval($upper), intval($step));
}

function repeat($obj, $times = 2)
{
    return str_repeat(strval($obj), $times);
}

function replace($obj = null, $search = '', $replace = '', $regex = false)
{
    if ($regex) {
        return preg_replace($search, $replace, strval($obj));
    } else {
        return str_replace($search, $replace, strval($obj));
    }
}

function strip_tags($obj = null, $allowableTags = '')
{
    return \strip_tags(strval($obj), $allowableTags);
}

function title($obj = null)
{
    return ucwords(strval($obj));
}
function trim($obj = null, $charlist = " \t\n\r\0\x0B")
{
    return \trim(strval($obj), $charlist);
}
function truncate($obj = null, $length = 255, $preserve_words = false,
    $hellip = '&hellip;')
{
    $obj = strval($obj);

    $truncated = $preserve_words ?
        preg_replace('/\s+?(\S+)?$/', '', substr($obj, 0, $length + 1)) :
        substr($obj, 0, $length);

    if (strlen($obj) > $length) {
        $truncated .= $hellip;
    }
    return $truncated;
}
function unescape($obj = null)
{
    return htmlspecialchars_decode(strval($obj), ENT_QUOTES);
}
function upper($obj = null)
{
    return strtoupper(strval($obj));
}
function url_encode($obj = null)
{
    return urlencode(strval($obj));
}
function word_wrap($obj = null, $width = 75, $break = "\n", $cut = false)
{
    return wordwrap(strval($obj), $width, $break, $cut);
}