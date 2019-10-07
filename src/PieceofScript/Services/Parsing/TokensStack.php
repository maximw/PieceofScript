<?php


namespace PieceofScript\Services\Parsing;


use PieceofScript\Services\Errors\Parser\EvaluationError;
use PieceofScript\Services\Errors\RuntimeError;

class TokensStack
{
    /** @var Token[]  */
    protected $stack = [];

    /**
     * Push new Token to stack
     * @param Token $token
     * @throws \Exception
     */
    public function push(Token $token)
    {
        array_push($this->stack, $token);
    }

    /**
     * Return the last element
     *
     * @return Token
     * @throws \Exception
     */
    public function head(): Token
    {
        $token = end($this->stack);
        if (!$token instanceof Token) {
            throw new RuntimeError('Something went wrong. Token stack is empty.');
        }
        return $token;
    }

    /**
     * Return penultimate element
     *
     * @return Token
     */
    public function neck(): Token
    {
        end($this->stack);
        return prev($this->stack);
    }

    /**
     * @return Token
     * @throws \Exception
     */
    public function pop(): Token
    {
        $token = array_pop($this->stack);
        if (!$token instanceof Token) {
            throw new EvaluationError('Something went wrong. Token stack is empty.');
        }
        return $token;
    }

    /**
     * Check if stack is empty
     *
     * @return bool
     */
    public function isEmpty():bool
    {
        return empty($this->stack);
    }

}