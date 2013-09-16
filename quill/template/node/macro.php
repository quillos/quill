<?php namespace Quill\Template;

class MacroNode extends Node
{
    protected $name;
    protected $args;
    protected $body;

    public function __construct($name, $args, $body, $line)
    {
        parent::__construct($line);
        $this->name = $name;
        $this->args = $args;
        $this->body = $body;
    }

    public function compile($compiler, $indent = 0)
    {
        $compiler->raw("\n");
        $compiler->addTraceInfo($this, $indent, false);
        $compiler->raw(
            'public function macro_' . $this->name .
            '($_context = array(), $macros = array())' . "\n", $indent
        );
        $compiler->raw("{\n", $indent);

        $compiler->raw('$context = $_context + array(' . "\n", $indent + 1);
        $i = 0;
        foreach ($this->args as $key => $val) {
            $compiler->raw(
                "'$key' => !isset(\$_context['$key']) &&" .
                " isset(\$_context[$i]) ? \$_context[$i] : ",
                $indent + 2
            );
            $val->compile($compiler);
            $compiler->raw(",\n");
            $i += 1;
        }
        $compiler->raw(");\n", $indent + 1);

        $compiler->raw("ob_start();\n", $indent + 1);
        $this->body->compile($compiler, $indent + 1);
        $compiler->raw("return ob_get_clean();\n", $indent + 1);
        $compiler->raw("}\n", $indent);
    }
}