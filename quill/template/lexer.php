<?php namespace Quill\Template;

class Lexer
{
    protected $source;
    protected $line;
    protected $cursor;
    protected $position;
    protected $queue;
    protected $end;
    protected $trim;

    const BLOCK_START_TAG      = '{%';
    const BLOCK_START_TAG_TRIM = '{%-';
    const BLOCK_END_TAG        = '%}';
    const BLOCK_END_TAG_TRIM   = '-%}';

    const COMMENT_START_TAG      = '{#';
    const COMMENT_START_TAG_TRIM = '{#-';
    const COMMENT_END_TAG        = '#}';
    const COMMENT_END_TAG_TRIM   = '-#}';

    const OUTPUT_START_TAG      = '{{';
    const OUTPUT_START_TAG_TRIM = '{{-';
    const OUTPUT_END_TAG        = '}}';
    const OUTPUT_END_TAG_TRIM   = '-}}';

    const POSITION_TEXT  = 0;
    const POSITION_BLOCK = 1;
    const POSITION_OUTPUT   = 2;

    const REGEX_CONSTANT = '/true\b | false\b | null\b/Ax';
    const REGEX_NAME     = '/[a-zA-Z_][a-zA-Z0-9_]*/A';
    const REGEX_NUMBER   = '/[0-9][0-9_]*(?:\.[0-9][0-9_]*)?/A';
    const REGEX_STRING   = '/(?:"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|
        \'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\')/Axsmu';
    const REGEX_OPERATOR = '/and\b|xor\b|or\b|not\b|in\b|
        =>|<>|<=?|>=?|[!=]==|[!=]?=|\.\.|[\[\]().,%*\/+|?:\-@]/Ax';

    public function __construct($name, $source)
    {
        $this->name     = $name;
        $this->source   = preg_replace("/(\r\n|\r|\n)/", "\n", $source);
        $this->line     = 1;
        $this->cursor   = 0;
        $this->position = self::POSITION_TEXT;
        $this->queue    = array();
        $this->end      = strlen($this->source);
        $this->trim     = false;
    }

    public function tokenize()
    {
        do {
            $tokens[] = $token = $this->next();
        } while ($token->getType() !== Token::EOF_TYPE);

        return new Stream($this->name, $tokens);
    }

    protected function next()
    {
        if (!empty($this->queue)) {
            return array_shift($this->queue);
        }

        if ($this->cursor >= $this->end) {
            return Token::tokenEOF('', $this->line);
        }

        switch ($this->position) {

        case self::POSITION_TEXT:
            $this->queue = $this->lexText();
            break;

        case self::POSITION_BLOCK:
            $this->queue = $this->lexBlock();
            break;

        case self::POSITION_OUTPUT:
            $this->queue = $this->lexOutput();
            break;
        }

        return $this->next();
    }

    protected function lexText()
    {
        $match = null;
        $tokens = array();

        // all text
        if (!preg_match('/(.*?)(' .
            preg_quote(self::COMMENT_START_TAG_TRIM) .'|' .
            preg_quote(self::COMMENT_START_TAG) . '|' .
            preg_quote(self::OUTPUT_START_TAG_TRIM) . '|' .
            preg_quote(self::OUTPUT_START_TAG) . '|' .
            preg_quote(self::BLOCK_START_TAG_TRIM) . '|' .
            preg_quote(self::BLOCK_START_TAG) . ')/As', $this->source, $match,
                null, $this->cursor)
        ) {
            $text = substr($this->source, $this->cursor);
            if ($this->trim) {
                $text = preg_replace("/^[ \t]*\n?/", '', $text);
                $this->trim = false;
            }
            $tokens[] = Token::tokenText($text, $this->line);
            $this->line += substr_count($text, "\n");
            $this->cursor = $this->end;
            return $tokens;
        }

        $this->cursor += strlen($match[0]);

        $line = $this->line;
        $this->line += substr_count($match[0], "\n");

        // text first
        $text  = $match[1];
        $token = $match[2];

        if (strlen($text)) {
            if ($this->trim) {
                $text = preg_replace("/^[ \t]*\n?/", '', $text);
                $this->trim = false;
            }
            if ($token == self::COMMENT_START_TAG_TRIM) {
                $tokens[] = Token::tokenText(rtrim($text, ' '), $line);
            } elseif ($token == self::BLOCK_START_TAG_TRIM) {
                $tokens[] = Token::tokenText(rtrim($text, ' '), $line);
            } elseif ($token == self::OUTPUT_START_TAG_TRIM) {
                $tokens[] = Token::tokenText(rtrim($text, ' '), $line);
            } else {
                $tokens[] = Token::tokenText($text, $line);
            }
            $line += substr_count($text, "\n");
        }

        switch ($token) {

        case self::COMMENT_START_TAG_TRIM:
        case self::COMMENT_START_TAG:
            if (preg_match('/.*?(' .
                preg_quote(self::COMMENT_END_TAG_TRIM) . '|' .
                preg_quote(self::COMMENT_END_TAG) . ')/As',
                    $this->source, $match, null, $this->cursor)
            ) {
                if ($match[1] == self::COMMENT_END_TAG_TRIM) {
                    $this->trim = true;
                }
                $this->cursor += strlen($match[0]);
                $this->line += substr_count($match[0], "\n");
            }
            break;

        case self::BLOCK_START_TAG_TRIM:
        case self::BLOCK_START_TAG:
            if (preg_match('/\s*raw\s*(' .
                preg_quote(self::BLOCK_END_TAG_TRIM) . '|' .
                preg_quote(self::BLOCK_END_TAG) . ')(.*?)(' .
                preg_quote(self::BLOCK_START_TAG_TRIM) . '|' .
                preg_quote(self::BLOCK_START_TAG) . ')\s*endraw\s*(' .
                preg_quote(self::BLOCK_END_TAG_TRIM) . '|' .
                preg_quote(self::BLOCK_END_TAG) . ')/As',
                    $this->source, $match, null, $this->cursor)
            ) {
                $raw = $match[2];
                if ($match[1] == self::BLOCK_END_TAG_TRIM) {
                    $raw = preg_replace("/^[ \t]*\n?/", '', $text);
                }
                if ($match[3] == self::BLOCK_START_TAG_TRIM) {
                    $raw = rtrim($raw, ' ');
                }
                if ($match[4] == self::BLOCK_END_TAG_TRIM) {
                    $this->trim = true;
                }
                $this->cursor += strlen($match[0]);
                $this->line += substr_count($match[0], "\n");
                $tokens[] = Token::tokenText($raw, $line);
                $this->position = self::POSITION_TEXT;
            } else {
                $tokens[] = Token::tokenBlockStart($token, $line);
                $this->position = self::POSITION_BLOCK;
            }
            break;

        case self::OUTPUT_START_TAG_TRIM:
        case self::OUTPUT_START_TAG:
            $tokens[] = Token::tokenOutputStart($token, $line);
            $this->position = self::POSITION_OUTPUT;
            break;

        }

        return $tokens;
    }

    protected function lexBlock()
    {
        $match = null;

        if (preg_match('/\s*(' .
            preg_quote(self::BLOCK_END_TAG_TRIM) . '|' .
            preg_quote(self::BLOCK_END_TAG) . ')/A',
                $this->source, $match, null, $this->cursor)
        ) {
            if ($match[1] == self::BLOCK_END_TAG_TRIM) {
                $this->trim = true;
            }
            $this->cursor += strlen($match[0]);
            $line = $this->line;
            $this->line += substr_count($match[0], "\n");
            $this->position = self::POSITION_TEXT;
            return array(Token::tokenBlockEnd($match[1], $line));
        }
        return $this->lexExpression();
    }

    protected function lexOutput()
    {
        $match = null;

        if (preg_match('/\s*(' .
            preg_quote(self::OUTPUT_END_TAG_TRIM) . '|' .
            preg_quote(self::OUTPUT_END_TAG) . ')/A',
                $this->source, $match, null, $this->cursor)
        ) {
            if ($match[1] == self::OUTPUT_END_TAG_TRIM) {
                $this->trim = true;
            }
            $this->cursor += strlen($match[0]);
            $line = $this->line;
            $this->line += substr_count($match[0], "\n");
            $this->position = self::POSITION_TEXT;
            return array(Token::tokenOutputEnd($match[1], $line));
        }
        return $this->lexExpression();
    }

    protected function lexExpression()
    {
        $match = null;

        // eat whitespace
        if (preg_match('/\s+/A', $this->source, $match, null, $this->cursor)) {
            $this->cursor += strlen($match[0]);
            $this->line += substr_count($match[0], "\n");
        }

        if (preg_match(self::REGEX_NUMBER, $this->source, $match, null,
            $this->cursor)
        ) {
            $this->cursor += strlen($match[0]);
            return array(Token::tokenNumber(
                str_replace('_', '', $match[0]), $this->line)
            );

        } elseif (preg_match(self::REGEX_OPERATOR, $this->source, $match, null,
            $this->cursor)
        ) {
            $this->cursor += strlen($match[0]);
            return array(Token::tokenOperator($match[0], $this->line));

        } elseif (preg_match(self::REGEX_CONSTANT, $this->source, $match, null,
            $this->cursor)
        ) {
            $this->cursor += strlen($match[0]);
            return array(Token::tokenConstant($match[0], $this->line));

        } elseif (preg_match(self::REGEX_NAME, $this->source, $match, null,
            $this->cursor)
        ) {
            $this->cursor += strlen($match[0]);
            return array(Token::tokenName($match[0], $this->line));

        } elseif (preg_match(self::REGEX_STRING, $this->source, $match, null,
            $this->cursor)
        ) {
            $this->cursor += strlen($match[0]);
            $this->line += substr_count($match[0], "\n");
            $value = stripcslashes(substr($match[0], 1, strlen($match[0]) - 2));
            return array(Token::tokenString($value, $this->line));

        } elseif ($this->position == self::POSITION_BLOCK &&
            preg_match('/(.+?)\s*(' .
            preg_quote(self::BLOCK_END_TAG_TRIM) . '|' .
            preg_quote(self::BLOCK_END_TAG) . ')/As',
                $this->source, $match, null, $this->cursor)
        ) {
            // a catch-all text token
            $this->cursor += strlen($match[1]);
            $line = $this->line;
            $this->line += substr_count($match[1], "\n");
            return array(Token::tokenText($match[1], $line));

        } elseif ($this->position == self::POSITION_OUTPUT &&
            preg_match('/(.+?)\s*(' . preg_quote(self::OUTPUT_END_TAG) . ')/As',
                $this->source, $match, null, $this->cursor)
        ) {
            $this->cursor += strlen($match[1]);
            $line = $this->line;
            $this->line += substr_count($match[1], "\n");
            return array(Token::tokenText($match[1], $line));

        } else {
            $text = substr($this->source, $this->cursor);
            $this->cursor += $this->end;
            $line = $this->line;
            $this->line += substr_count($text, "\n");
            return array(Token::tokenText($text, $line));
        }
    }
}