<?php namespace Quill\Template;

use Exception;

class SyntaxError extends Exception
{
    public function __construct($message, $name, $line)
    {
        parent::__construct($message . ' in ' . $name . ' line ' . $line);
    }
}