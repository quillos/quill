<?php namespace Quill\Template;

class IncludeNode extends Node
{
    protected $include;

    public function __construct($include, $line)
    {
        parent::__construct($line);
        $this->include = $include;
    }

    public function compile($compiler, $indent = 0)
    {
        $compiler->addTraceInfo($this, $indent);
        $compiler->raw('$this->loadInclude(', $indent);
        $this->include->compile($compiler);
        $compiler->raw(')->display($context);' . "\n");
    }
}