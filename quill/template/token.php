<?php namespace Quill\Template;

class Token
{
    protected $type;
    protected $value;
    protected $line;
    const EOF_TYPE          = -1;
    const TEXT_TYPE         = 0;
    const BLOCK_START_TYPE  = 1;
    const OUTPUT_START_TYPE = 2;
    const BLOCK_END_TYPE    = 3;
    const OUTPUT_END_TYPE   = 4;
    const NAME_TYPE         = 5;
    const NUMBER_TYPE       = 6;
    const STRING_TYPE       = 7;
    const OPERATOR_TYPE     = 8;
    const CONSTANT_TYPE     = 9;
    public function __construct($type, $value, $line)
    {
        $this->type  = $type;
        $this->value = $value;
        $this->line  = $line;
    }
    public static function getTypeAsString($type, $canonical = false)
    {
        if (is_string($type)) {
            return $canonical ? (__CLASS__ . '::' . $type) : $type;
        }

        switch ($type) {
        case self::EOF_TYPE:
            $name = 'EOF_TYPE';
            break;
        case self::TEXT_TYPE:
            $name = 'TEXT_TYPE';
            break;
        case self::BLOCK_START_TYPE:
            $name = 'BLOCK_START_TYPE';
            break;
        case self::OUTPUT_START_TYPE:
            $name = 'OUTPUT_START_TYPE';
            break;
        case self::BLOCK_END_TYPE:
            $name = 'BLOCK_END_TYPE';
            break;
        case self::OUTPUT_END_TYPE:
            $name = 'OUTPUT_END_TYPE';
            break;
        case self::NAME_TYPE:
            $name = 'NAME_TYPE';
            break;
        case self::NUMBER_TYPE:
            $name = 'NUMBER_TYPE';
            break;
        case self::STRING_TYPE:
            $name = 'STRING_TYPE';
            break;
        case self::OPERATOR_TYPE:
            $name = 'OPERATOR_TYPE';
            break;
        case self::CONSTANT_TYPE:
            $name = 'CONSTANT_TYPE';
            break;
        }
        return $canonical ? (__CLASS__ . '::' . $name) : $name;
    }
    public static function getTypeError($type)
    {
        switch ($type) {
        case self::EOF_TYPE:
            $name = 'end of file';
            break;
        case self::TEXT_TYPE:
            $name = 'text type';
            break;
        case self::BLOCK_START_TYPE:
            $name = 'block start (either "{%" or "{%-")';
            break;
        case self::OUTPUT_START_TYPE:
            $name = 'block start (either "{{" or "{{-")';
            break;
        case self::BLOCK_END_TYPE:
            $name = 'block end (either "%}" or "-%}")';
            break;
        case self::OUTPUT_END_TYPE:
            $name = 'block end (either "}}" or "-}}")';
            break;
        case self::NAME_TYPE:
            $name = 'name type';
            break;
        case self::NUMBER_TYPE:
            $name = 'number type';
            break;
        case self::STRING_TYPE:
            $name = 'string type';
            break;
        case self::OPERATOR_TYPE:
            $name = 'operator type';
            break;
        case self::CONSTANT_TYPE:
            $name = 'constant type (true, false, or null)';
            break;
        }
        return $name;
    }
    public function test($type, $values = null)
    {
        if (is_null($values) && !is_int($type)) {
            $values = $type;
            $type = self::NAME_TYPE;
        }

        return ($this->type === $type) && (
            is_null($values) ||
            (is_array($values) && in_array($this->value, $values)) ||
            $this->value == $values
        );
    }
    public function getType($asString = false, $canonical = false)
    {
        if ($asString) {
            return self::getTypeAsString($this->type, $canonical);
        }
        return $this->type;
    }
    public function getValue()
    {
        return $this->value;
    }
    public function getLine()
    {
        return $this->line;
    }
    public function __toString()
    {
        return $this->getValue();
    }
    public static function tokenEOF($value, $line)
    {
        return new self(self::EOF_TYPE, $value, $line);
    }
    public static function tokenText($value, $line)
    {
        return new self(self::TEXT_TYPE, $value, $line);
    }
    public static function tokenBlockStart($value, $line)
    {
        return new self(self::BLOCK_START_TYPE, $value, $line);
    }
    public static function tokenOutputStart($value, $line)
    {
        return new self(self::OUTPUT_START_TYPE, $value, $line);
    }
    public static function tokenBlockEnd($value, $line)
    {
        return new self(self::BLOCK_END_TYPE, $value, $line);
    }
    public static function tokenOutputEnd($value, $line)
    {
        return new self(self::OUTPUT_END_TYPE, $value, $line);
    }
    public static function tokenName($value, $line)
    {
        return new self(self::NAME_TYPE, $value, $line);
    }
    public static function tokenNumber($value, $line)
    {
        return new self(self::NUMBER_TYPE, $value, $line);
    }
    public static function tokenString($value, $line)
    {
        return new self(self::STRING_TYPE, $value, $line);
    }
    public static function tokenOperator($value, $line)
    {
        return new self(self::OPERATOR_TYPE, $value, $line);
    }
    public static function tokenConstant($value, $line)
    {
        return new self(self::CONSTANT_TYPE, $value, $line);
    }
}