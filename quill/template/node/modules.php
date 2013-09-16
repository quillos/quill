<?php namespace Quill\Template;

class ModulesNode extends Node
{
    protected $name;
    protected $args;

    public function __construct($name, $args, $line)
    {
        parent::__construct($line);
        $this->name = $name;
        $this->args = $args;
    }

    public function compile($compiler, $indent = 0)
    {
        $compiler->addTraceInfo($this, $indent);
        $compiler->raw('$this->loadModule(\'' . $this->name . '\', ', $indent);
        if (is_array($this->args)) {
            $compiler->raw('array(');
            foreach ($this->args as $arg) {
                $arg->compile($compiler);
                $compiler->raw(', ');
            }
            $compiler->raw(')');
        } else {
            $compiler->raw('false');
        }
        $compiler->raw(');' . "\n");
    }
}