<?php namespace Quill\Template;

use Exception;
use RuntimeException;

class Loader
{
    const CLASS_PREFIX = '__Template_';
    const RECOMPILE_NEVER = -1;
    const RECOMPILE_NORMAL = 0;
    const RECOMPILE_ALWAYS = 1;
    protected $options;
    protected $paths;
    protected $cache;
    public function __construct($options)
    {
        if (!isset($options['source'])) {
            throw new RuntimeException('missing source directory');
        }
        if (!isset($options['target'])) {
            throw new RuntimeException('missing target directory');
        }
        $options += array(
            'mode'    => self::RECOMPILE_NORMAL,
            'mkdir'   => 0777,
        );
        if (!($target = realpath($options['target'])) || !is_dir($target)) {
            if ($options['mkdir'] === false) {
                throw new RuntimeException(sprintf(
                    'target directory %s not found',
                    $options['target']
                ));
            }
            if (!mkdir($options['target'], $options['mkdir'], true)) {
                throw new RuntimeException(sprintf(
                    'unable to create target directory %s',
                    $options['target']
                ));
            }
        }
        $this->options = array(
            'source'  => $options['source'],
            'target'  => $target,
            'mode'    => $options['mode'],
        );
        $this->paths = array();
        $this->cache = array();
    }
    public function load($template, $from = null)
    {
        if ($template instanceof Template) {
            return $template;
        }
        $source  = $this->options['source'];

        if (is_readable($template))
        {
            $path = $template;
        } else {
            if (is_null($from)) {
                $path = $source . '/' . $template;
            } else {
                if (is_dir($from)) {
                    $path = $from . '/' . $template;
                } else {
                    $path = dirname($from) . '/' . $template;
                }
            }
            if (!$path = realpath($path)) {
                throw new RuntimeException(sprintf(
                    '%s is not a valid readable template',
                    $path . '|' . is_dir($from)
                ));
            }
        }
        $class = self::CLASS_PREFIX . md5($path);
        if (isset($this->cache[$class])) {
            return $this->cache[$class];
        }
        if (!class_exists($class, false)) {
            $target = $this->options['target'] . '/' . $class . '.php';
            switch ($this->options['mode']) {
                case self::RECOMPILE_ALWAYS:
                    $compile = true;
                    break;
                case self::RECOMPILE_NEVER:
                    $compile = !file_exists($target);
                    break;
                case self::RECOMPILE_NORMAL:
                default:
                    $compile = !file_exists($target) ||
                        filemtime($target) < filemtime($path);
                    break;
            }
            if ($compile) {
                $lexer    = new Lexer($path, file_get_contents($path));
                $parser   = new Parser($lexer->tokenize());
                $compiler = new Compiler($parser->parse());
                $compiler->compile($target);
            }
            require_once $target;
        }
        $this->cache[$class] = new $class($this);
        return $this->cache[$class];
    }
}