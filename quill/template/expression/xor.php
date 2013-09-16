<?php namespace Quill\Template;

class XorExpression extends BinaryExpression
{
    public function operator()
    {
        return 'xor';
    }
}