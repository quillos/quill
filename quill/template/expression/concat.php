<?php namespace Quill\Template;

class ConcatExpression extends BinaryExpression
{
    public function operator()
    {
        return '.';
    }
}