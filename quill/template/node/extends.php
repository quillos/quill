<?php namespace Quill\Template;

class ExtendsNode extends Node
{
    protected $parent;

    public function __construct($parent, $line)
    {
        parent::__construct($line);
        $this->parent = $parent;
    }

    public function compile($compiler, $indent = 0)
    {
        $compiler->addTraceInfo($this, $indent);
        $compiler->raw('$this->parent = $this->loadExtends(', $indent);
        $this->parent->compile($compiler);
        $compiler->raw(');' . "\n");

        $compiler->raw('if (isset($this->parent)) {' . "\n", $indent);
        $compiler->raw(
            'return $this->parent->display' .
            '($context, $blocks + $this->blocks, $macros + $this->macros);'.
            "\n", $indent + 1
        );
        $compiler->raw("}\n", $indent);
    }
}