<?php namespace Quill;

use Exception;
use ErrorException;

class Error
{
    public static function register()
    {
        set_exception_handler(array('Quill\\Error', 'exception'));
        set_error_handler(array('Quill\\Error', 'native'));
        register_shutdown_function(array('Quill\\Error', 'shutdown'));
    }
    public static function unregister()
    {
        restore_exception_handler();
        restore_error_handler();
    }
    public static function exception(Exception $e)
    {
        ob_get_level() and ob_end_clean();
        $message = $e->getMessage();
        $file = $e->getFile();
        echo "<html><h2>Unhandled Exception</h2>
            <h3>Message:</h3>
            <pre>{$message}</pre>
            <h3>Location:</h3>
            <pre>{$file} on line {$e->getLine()}</pre>
            <h3>Stack Trace:</h3>
            <pre>{$e->getTraceAsString()}</pre>
        </html>";
        exit(1);
    }
    public static function native($code, $message, $file, $line)
    {
        if (error_reporting() === 0) return;
        static::exception(new ErrorException($message, $code, 0, $file, $line));
    }
    public static function shutdown()
    {
        if($error = error_get_last())
        {
            extract($error);
            static::native($type, $message, $file, $line);
        }
    }
    public static function log($e){}
}