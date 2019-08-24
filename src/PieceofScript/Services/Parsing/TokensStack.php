<?php


namespace PieceofScript\Services\Parsing;


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
            throw new \Exception('Something went wrong. Token stack is empty.');
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

    public function pop(): Token
    {
        $token = array_pop($this->stack);
        if (!$token instanceof Token) {
            throw new \Exception('Something went wrong. Token stack is empty.');
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

    public function debug()
    {
        echo PHP_EOL;
        foreach ($this->stack as $item) {
            if ($item->getName() === Evaluator::T_ARRAY_SUB_AST) {
                echo ' [ ';
                $item->getValue()->debug();
                echo ' ] ';
            } elseif ($item->getName() === Evaluator::T_ARRAY_KEY) {
                echo '.' . $item->getValue() . ' ';
            } else {
                echo $item->getValue() . ' ';
            }
        }
        echo PHP_EOL;
    }
}