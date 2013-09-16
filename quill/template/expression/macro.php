<?php namespace Quill\Template;

class MacroExpression extends Expression
{
    protected $module;
    protected $name;
    protected $args;

    public function __construct($module, $name, $args, $line)
    {
        parent::__construct($line);
        $this->module = $module;
        $this->name = $name;
        $this->args = $args;
    }

    public function compile($compiler, $indent = 0)
    {
        $compiler->raw(
            '$this->expandMacro(\'' . $this->name . '\', array(', $indent
        );
        foreach ($this->args as $key => $val) {
            $compiler->raw("'$key' => ");
            $val->compile($compiler);
            $compiler->raw(',');
        }
        if (isset($this->module)) {
            $compiler->raw(
                '), $this->imports[\'' . $this->module . '\']->macros)'
            );
        } else {
            $compiler->raw('), $macros)');
        }
    }
}