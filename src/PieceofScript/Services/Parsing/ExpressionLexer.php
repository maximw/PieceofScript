<?php


namespace PieceofScript\Services\Parsing;


use PieceofScript\Services\Errors\RuntimeError;

class ExpressionLexer
{
    protected $lexemes = [
        '~^([a-z][a-z0-9\\\\_]*)(?=\s*\()~iu' => Token::T_GENERATOR_NAME,
        '~^\\$[a-z][a-z0-9_]*~iu' => Token::T_VARIABLE,
        '~^\\@[a-z][a-z0-9_]*~iu' => Token::T_VARIABLE_TYPE,
        '~^\.(([a-z][a-z0-9_]*)|([0-9]+))~iu' => Token::T_ARRAY_KEY,

        '~^"(?:[^"\\\\]|\\\\.)*"~u' => Token::T_STRING_DOUBLE,
        "~^'(?:[^'\\\\]|\\\\.)*'~u" => Token::T_STRING,
        '~^(?:[0-9]+(?:[\.][0-9]+)*)(?:e[+-]?[0-9]+)?~u' => Token::T_NUMBER,
        '~^(true|false)~iu' => Token::T_BOOL,
        '~^null~iu' => Token::T_NULL,

        '~^\\(~u' => Token::T_OPEN_PARENTHESIS,
        '~^\\)~u' => Token::T_CLOSE_PARENTHESIS,
        '~^\\[~u' => Token::T_OPEN_BRACKETS,
        '~^\\]~u' => Token::T_CLOSE_BRACKETS,
        '~^\\,~u' => Token::T_COMMA,
        '~^\\s+~u' => Token::T_SPACE,
        '~^//.*$~u' => Token::T_COMMENT,
        '~^=(?!\=)~u' => Token::T_ASSIGNMENT,
        '~^;~u' => Token::T_SEMICOLON,

        '~^==~u' => Token::T_EQUALS,
        '~^>(?!\=)~u' => Token::T_GREATER_THAN,
        '~^<(?!\=)~u' => Token::T_LOWER_THAN,
        '~^!=~u' => Token::T_NOT_EQUALS,
        '~^>=~u' => Token::T_GREATER_EQUAL,
        '~^<=~u' => Token::T_LOWER_EQUAL,
        '~^\\+~u' => Token::T_PLUS,
        '~^\\-~u' => Token::T_MINUS,
        '~^\\*~u' => Token::T_MULTIPLY,
        '~^\\/~u' => Token::T_DIVIDE,
        '~^\\%~u' => Token::T_DIVIDE_MOD,
        '~^\\^~u' => Token::T_POWER,
        '~^\\|\\|~u' => Token::T_OR,
        '~^&&~u' => Token::T_AND,
        '~^!(?!\=)~u' => Token::T_NOT,
    ];


    /**
     * Split string expression to lexical tokens
     *
     * @param string $expression
     * @return TokensQueue
     * @throws \Exception
     */
    public function tokenize(string $expression): TokensQueue
    {
        $tokens = new TokensQueue();
        $offset = 0;
        $matches = null;
        $expressionCopy = $expression;
        while (mb_strlen($expressionCopy, 'UTF-8')) {
            $anyMatch = false;
            foreach ($this->lexemes as $regex => $lexemeName) {
                if (preg_match($regex, $expressionCopy, $matches)) {
                    $value = $matches[0];
                    $len = mb_strlen($value, 'UTF-8');

                    if (Token::TYPES[$lexemeName] !== Token::TYPE_IGNORED) {
                        if ($lexemeName === Token::T_STRING_DOUBLE) {
                            $value = trim($value, '"');
                        } elseif ($lexemeName === Token::T_STRING) {
                            $value = trim($value, "'");
                        } elseif ($lexemeName === Token::T_ARRAY_KEY) {
                            $value = ltrim($value, '.');
                        }

                        // Unary positive and negative
                        if ($tokens->isEmpty()
                            || $tokens->head()->getName() === Token::T_OPEN_PARENTHESIS
                            || $tokens->head()->getName() === Token::T_OPEN_BRACKETS
                            || $tokens->head()->getName() === Token::T_COMMA
                            || $tokens->head()->getType() === Token::TYPE_OPERATION
                            || $tokens->head()->getType() === Token::TYPE_ASSIGNMENT
                        ) {
                            if ($lexemeName === Token::T_MINUS) {
                                $lexemeName = Token::T_NEGATIVE;
                            }
                            if ($lexemeName === Token::T_PLUS) {
                                $lexemeName = Token::T_POSITIVE;
                            }
                        }

                        $token = new Token($lexemeName, $value, $offset);
                        $tokens->add($token);
                    }

                    $expressionCopy = mb_substr($expressionCopy, $len, null, 'UTF-8');
                    $anyMatch = true;
                    $offset += $len;
                    break;
                }
            }
            if (!$anyMatch) {
                throw new RuntimeError(sprintf('Cannot parse expression at offset %s: %s', $offset, $expression));
            }
        }

        return $tokens;
    }

}