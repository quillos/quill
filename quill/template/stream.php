<?php namespace Quill\Template;

class Stream
{
    protected $name;
    protected $tokens;
    protected $currentToken;
    protected $queue;
    protected $cursor;
    protected $eos;

    public function __construct($name, array $tokens)
    {
        $this->name = $name;
        $this->tokens = $tokens;
        $this->currentToken = null;
        $this->queue = array();
        $this->cursor = 0;
        $this->eos = false;
        $this->next();
    }

    public function getName()
    {
        return $this->name;
    }

    public function next($queue = true)
    {
        if ($this->eos) {
            return $this->currentToken;
        }

        $token = $this->tokens[$this->cursor++];

        $old = $this->currentToken;

        $this->currentToken = $token;

        $this->eos = ($token->getType() === Token::EOF_TYPE);

        return $old;
    }

    public function look($t = 1)
    {
        $t--;
        $length = count($this->tokens);
        if ($this->cursor + $t > $length) $t = 0;
        if ($this->cursor + $t < 0) $t = -$this->cursor;
        return $this->tokens[$this->cursor + $t];
    }

    public function skip($times = 1)
    {
        for ($i = 0; $i < $times; $i++) {
            $this->next();
        }
        return $this;
    }

    public function expect($primary, $secondary = null)
    {
        $token = $this->getCurrentToken();
        if (is_null($secondary) && !is_int($primary)) {
            $secondary = $primary;
            $primary = Token::NAME_TYPE;
        }
        if (!$token->test($primary, $secondary)) {
            if (is_null($secondary)) {
                $expecting = Token::getTypeError($primary);
            } elseif (is_array($secondary)) {
                $expecting = '"' . implode('" or "', $secondary) . '"';
            } else {
                $expecting = '"' . $secondary . '"';
            }
            if ($token->getType() === Token::EOF_TYPE) {
                throw new SyntaxError(
                    'unexpected end of file',
                    $this->name, $token->getLine() - 1
                );
            } else {
                throw new SyntaxError(
                    sprintf(
                        'unexpected "%s", expecting %s',
                        str_replace("\n", '\n', $token->getValue()), $expecting
                    ),
                    $this->name, $token->getLine()
                );
            }
        }
        $this->next();
        return $token;
    }

    public function expectTokens($tokens)
    {
        foreach ($tokens as $token) {
            $this->expect($token->getType(), $token->getValue());
        }
        return $this;
    }

    public function test($primary, $secondary = null)
    {
        return $this->getCurrentToken()->test($primary, $secondary);
    }

    public function consume($primary, $secondary = null)
    {
        if ($this->test($primary, $secondary)) {
            $this->expect($primary, $secondary);
            return true;
        } else {
            return false;
        }
    }

    public function isEOS()
    {
        return $this->eos;
    }

    public function getCurrentToken()
    {
        return $this->currentToken;
    }

    public function getTokens()
    {
        return $this->tokens;
    }
}