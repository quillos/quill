<?php namespace Quill\Template;

class NegExpression extends UnaryExpression
{
    public function operator($compiler)
    {
        $compiler->raw('-');
    }
}