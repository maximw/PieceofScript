<?php


namespace PieceofScript\Services\Parsing;


use PieceofScript\Services\Errors\RuntimeError;

class TokensQueue
{
    /** @var Token[]  */
    protected $queue = [];

    /**
     * Add new Token to queue
     * @param Token $token
     */
    public function add(Token $token)
    {
        array_push($this->queue, $token);
    }

    /**
     * Return the first element
     *
     * @return Token
     * @throws RuntimeError
     */
    public function head(): Token
    {
        $token = reset($this->queue);
        if (!$token instanceof Token) {
            throw new RuntimeError('Something went wrong. Token stack is empty.');
        }
        return $token;
    }

    /**
     * Return the last element
     *
     * @return Token
     * @throws RuntimeError
     */
    public function tail(): Token
    {
        $token = end($this->queue);
        if (!$token instanceof Token) {
            throw new RuntimeError('Something went wrong. Token stack is empty.');
        }
        return $token;
    }

    /**
     * Retrieve Token from queue
     *
     * @return Token
     * @throws RuntimeError
     */
    public function pop(): Token
    {
        $token = array_pop($this->queue);
        if (!$token instanceof Token) {
            throw new RuntimeError('Something went wrong. Token stack is empty.');
        }
        return $token;
    }

    /**
     * Retrieve Token from queue
     *
     * @return Token
     * @throws RuntimeError
     */
    public function get(): Token
    {
        $token = array_shift($this->queue);
        if (!$token instanceof Token) {
            throw new RuntimeError('Something went wrong. Token stack is empty.');
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
        return empty($this->queue);
    }

}