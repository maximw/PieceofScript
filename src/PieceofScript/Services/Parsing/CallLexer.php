<?php


namespace PieceofScript\Services\Parsing;


use PieceofScript\Services\Call\ArgumentCallItem;
use PieceofScript\Services\Call\BaseCall;
use PieceofScript\Services\Call\BaseCallItem;
use PieceofScript\Services\Call\OptionsCallItem;
use PieceofScript\Services\Call\TextCallItem;

class CallLexer extends ExpressionLexer
{
    protected $callLexemes = [
        '~^\{{~u' => Token::T_CALL_ARGUMENT_DOUBLE_BRACKET_OPEN,
        '~^\}}~u' => Token::T_CALL_ARGUMENT_DOUBLE_BRACKET_CLOSE,
        '~^\{~u'  => Token::T_CALL_ARGUMENT_BRACKET_OPEN,
        '~^\}~u'  => Token::T_CALL_ARGUMENT_BRACKET_CLOSE,
    ];

    public function __construct()
    {
        $this->lexemes = array_merge($this->callLexemes, $this->lexemes);
    }


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


                    $expressionCopy = mb_substr($expressionCopy, $len, null, 'UTF-8');
                    $anyMatch = true;
                    $offset += $len;
                    break;
                }
            }
            if (!$anyMatch) {
                $len = 1;
                $value = mb_substr($expressionCopy, 0, $len, 'UTF-8');
                $expressionCopy = mb_substr($expressionCopy, $len, null, 'UTF-8');
                $offset += $len;
                $token = new Token(Token::T_CALL_WORD, $value, $offset);
                $tokens->add($token);
            }
        }

        return $tokens;
    }

    public function getCall(string $callString): BaseCall
    {
        $tokens = $this->tokenize($callString);
        $call = new BaseCall($callString);

        $tokens = $this->clearTokensQueue($tokens);

        while (!$tokens->isEmpty()) {
            if ($tokens->head()->getName() === Token::T_CALL_ARGUMENT_BRACKET_OPEN) {
                $callItem = $this->getNextArgumentItem($tokens);
            } elseif ($tokens->head()->getName() === Token::T_VARIABLE) {
                $callItem = $this->getNextVariableItem($tokens);
            } elseif ($tokens->head()->getName() === Token::T_CALL_ARGUMENT_DOUBLE_BRACKET_OPEN) {
                $callItem = $this->getNextOptionsItem($tokens);
            } else {
                $callItem = $this->getNextTextItem($tokens);
            }

            if ($callItem instanceof BaseCallItem) {
                $call->addItem($callItem);
            }
        }

        return $call;
    }

    protected function getNextTextItem(TokensQueue $tokens): ?TextCallItem
    {
        $callItem = new TextCallItem();

        while (!$tokens->isEmpty()
            && $tokens->head()->getName() !== Token::T_CALL_ARGUMENT_BRACKET_OPEN
            && $tokens->head()->getName() !== Token::T_CALL_ARGUMENT_DOUBLE_BRACKET_OPEN
            && $tokens->head()->getName() !== Token::T_VARIABLE
        ) {
            $token = $tokens->get();
            $callItem->addToken($token);
        }

        if (empty($callItem->getValue())) {
            return null;
        }

        return $callItem;
    }


    protected function getNextArgumentItem(TokensQueue $tokens): ArgumentCallItem
    {
        $callItem = new ArgumentCallItem(null, false);

        $tokens->get(); // Remove open brackets
        while ($tokens->head()->getName() !== Token::T_CALL_ARGUMENT_BRACKET_CLOSE) {
            $token = $tokens->get();
            $callItem->addToken($token);
        }
        $tokens->get(); // Remove close brackets

        return $callItem;
    }

    protected function getNextVariableItem(TokensQueue $tokens): ArgumentCallItem
    {
        $callItem = new ArgumentCallItem(null, true);

        while (!$tokens->isEmpty()
             && ($tokens->head()->getName() === Token::T_VARIABLE
                || $tokens->head()->getName() === Token::T_ARRAY_KEY)
        ) {
            $token = $tokens->get();
            $callItem->addToken($token);
        }

        return $callItem;
    }

    protected function getNextOptionsItem(TokensQueue $tokens): OptionsCallItem
    {
        $callItem = new OptionsCallItem();

        $tokens->get(); // Remove open brackets {{
        while ($tokens->head()->getName() !== Token::T_CALL_ARGUMENT_DOUBLE_BRACKET_CLOSE) {
            $token = $tokens->get();
            $callItem->addToken($token);
        }
        $tokens->get(); // Remove close brackets }}

        return $callItem;
    }

    protected function clearTokensQueue(TokensQueue $tokens): TokensQueue
    {
        $clearedTokens = new TokensQueue();
        while (!$tokens->isEmpty()) {
            $token = $tokens->get();
            if ($token->getName() !== Token::T_COMMENT) {
                $clearedTokens->add($token);
            }
        }
        return $clearedTokens;
    }
}