<?php namespace Quill\Template;

class ConstantExpression extends Expression
{
    protected $value;

    public function __construct($value, $line)
    {
        parent::__construct($line);
        $this->value = $value;
    }

    public function compile($compiler, $indent = 0)
    {
        $compiler->repr($this->value);
    }
}