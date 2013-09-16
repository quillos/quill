<?php namespace Quill\Template;

class BreakNode extends Node
{
    public function compile($compiler, $indent = 0)
    {
        $compiler->addTraceInfo($this, $indent);
        $compiler->raw("break;\n", $indent);
    }
}
