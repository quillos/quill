<?php namespace Quill\Template;

class AndExpression extends Logical
{
    public function compile($compiler, $indent = 0)
    {
        $compiler->raw('(!($a = ', $indent);
        $this->left->compile($compiler);
        $compiler->raw(') ? ($a) : (');
        $this->right->compile($compiler);
        $compiler->raw('))');
    }
}