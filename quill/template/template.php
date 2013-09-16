<?php namespace Quill\Template;

use RuntimeException;
use Exception;
use Quill\UI;

abstract class Template
{
    protected $loader;
    protected $parent;
    protected $blocks;
    protected $macros;
    protected $imports;
    protected $stack;

    public function __construct($loader)
    {
        $this->loader  = $loader;
        $this->parent  = null;
        $this->blocks  = array();
        $this->macros  = array();
        $this->imports = array();
        $this->stack   = array();
    }

    public function loadExtends($template)
    {
        try {
            return $this->loader->load($template, static::PATH);
        } catch (Exception $e) {
            throw new RuntimeException(sprintf(
                'error extending %s (%s) from %s line %d',
                $template, $e->getMessage(), static::PATH,
                $this->getLineTrace($e)
            ));
        }
    }

    public function loadInclude($template)
    {
        try {
            return $this->loader->load($template, static::PATH);
        } catch (Exception $e) {
            throw new RuntimeException(sprintf(
                'error including %s (%s) from %s line %d',
                $template, $e->getMessage(), static::PATH,
                $this->getLineTrace($e)
            ));
        }
    }

    public function loadModule($module, $args)
    {
        try {
            return UI::apply($module, $args);
        } catch (Exception $e) {
            throw new RuntimeException(sprintf(
                'error module %s (%s) from %s line %d',
                $module, $e->getMessage(), static::PATH,
                $this->getLineTrace($e)
            ));
        }  
    }

    public function loadImport($template)
    {
        try {
            return $this->loader->load($template, static::PATH);
        } catch (Exception $e) {
            throw new RuntimeException(sprintf(
                'error importing %s (%s) from %s line %d',
                $template, $e->getMessage(), static::PATH,
                $this->getLineTrace($e)
            ));
        }
    }

    public function displayBlock($name, $context, $blocks, $macros)
    {
        $blocks = $blocks + $this->blocks;
        if (isset($blocks[$name]) && is_callable($blocks[$name])) {
            return call_user_func($blocks[$name], $context, $blocks, $macros);
        }
    }

    public function displayParent($name, $context, $blocks, $macros)
    {
        $parent = $this;
        while ($parent = $parent->parent) {
            if (isset($parent->blocks[$name]) &&
                is_callable($parent->blocks[$name])) {
                return call_user_func($parent->blocks[$name], $context, $blocks,
                        $macros);
            }
        }
    }
    public function expandMacro($name, $context, $macros)
    {
        $macros = $macros + $this->macros;
        if (isset($macros[$name]) && is_callable($macros[$name])) {
            return call_user_func($macros[$name], $context, $macros);
        } else {
            throw new RuntimeException(
                sprintf(
                    'undefined macro "%s" in %s line %d',
                    $name, static::PATH, $this->getLineTrace()
                )
            );
        }
    }
    public function pushContext(&$context, $name)
    {
        if (!array_key_exists($name, $this->stack)) {
            $this->stack[$name] = array();
        }
        array_push($this->stack[$name], isset($context[$name]) ?
            $context[$name] : null
        );
        return $this;
    }
    public function popContext(&$context, $name)
    {
        if (!empty($this->stack[$name])) {
            $context[$name] = array_pop($this->stack[$name]);
        }
        return $this;
    }
    public function getLineTrace(Exception $e = null)
    {
        if (!isset($e)) {
            $e = new Exception;
        }

        $lines = static::$lines;

        $file = get_class($this) . '.php';

        foreach ($e->getTrace() as $trace) {
            if (isset($trace['file']) && basename($trace['file']) == $file) {
                $line = $trace['line'];
                return isset($lines[$line]) ? $lines[$line] : null;
            }
        }
        return null;
    }
    public function helper($name, $args = array())
    {
        $args = func_get_args();
        $name = array_shift($args);

        try {
            if (is_callable("\\Quill\\$name")) {
                return call_user_func_array("\\Quill\\$name", $args);
            } elseif (is_callable($name)) {
                return call_user_func_array($name, $args);
            }
        } catch (Exception $e) {
            throw new RuntimeException(
                sprintf(
                    '%s in %s line %d',
                    $e->getMessage(), static::PATH, $this->getLineTrace($e)
                )
            );
        }

        throw new RuntimeException(
            sprintf(
                'undefined helper "%s" in %s line %d',
                $name, static::PATH, $this->getLineTrace()
            )
        );

    }
    abstract public function display($context = array(), $blocks = array(),
        $macros = array());
    public function render($context = array(), $blocks = array(),
        $macros = array())
    {
        ob_start();
        $this->display($context, $blocks, $macros);
        return ob_get_clean();
    }
    public function iterate($context, $seq)
    {
        return new Context($seq, isset($context['loop']) ?
            $context['loop'] : null);
    }
    public function getModule($module)
    {
        return null;
    }
    public function getAttr($obj, $attr, $args = array())
    {
        if (is_array($obj)) {
            if (isset($obj[$attr])) {
                if ($obj[$attr] instanceof \Closure) {
                    if (is_array($args)) {
                        array_unshift($args, $obj);
                    } else {
                        $args = array($obj);
                    }
                    return call_user_func_array($obj[$attr], $args);
                } else {
                    return $obj[$attr];
                }
            } else {
                return null;
            }
        } elseif (is_object($obj)) {

            if (is_array($args)) {
                $callable = array($obj, $attr);
                return is_callable($callable) ?
                    call_user_func_array($callable, $args) : null;
            } else {
                $members = array_keys(get_object_vars($obj));
                $methods = get_class_methods(get_class($obj));
                if (in_array($attr, $methods)) {
                    $callable = array($obj, $attr);
                    return is_callable($callable) ?
                        call_user_func($callable) : null;
                } elseif (in_array($attr, $members)) {
                    return @$obj->$attr;
                } elseif (in_array('__get', $methods)) {
                    return $obj->__get($attr);
                } /*else {
                    $callable = array($obj, $attr);
                    return is_callable($callable) ?
                        call_user_func($callable) : null;
                }*/
            }

        } else {
            return null;
        }
    }
    public function setAttr(&$obj, $attrs, $value)
    {
        if (empty($attrs)) {
            $obj = $value;
            return;
        }
        $attr = array_shift($attrs);
        if (is_object($obj)) {
            $class = get_class($obj);
            $members = array_keys(get_object_vars($obj));
            if (!in_array($attr, $members)) {
                if (empty($attrs) && method_exists($obj, '__set')) {
                    $obj->__set($attr, $value);
                    return;
                } elseif (property_exists($class, $attr)) {
                    throw new RuntimeException(
                        "inaccessible '$attr' object attribute"
                    );
                } else {
                    if ($attr === null || $attr === false || $attr === '') {
                        if ($attr === null)  $token = 'null';
                        if ($attr === false) $token = 'false';
                        if ($attr === '')    $token = 'empty string';
                        throw new RuntimeException(
                            sprintf(
                                'invalid object attribute (%s) in %s line %d',
                                $token, static::PATH, $this->getLineTrace()
                            )
                        );
                    }
                    $obj->{$attr} = null;
                }
            }
            if (!isset($obj->$attr)) $obj->$attr = null;
            $this->setAttr($obj->$attr, $attrs, $value);
        } else {
            if (!is_array($obj)) $obj = array();
            $this->setAttr($obj[$attr], $attrs, $value);
        }
    }
}