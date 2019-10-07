<?php


namespace PieceofScript\Services\Errors\Parser;


use PieceofScript\Services\Errors\RuntimeError;
use PieceofScript\Services\Parsing\TokensStack;

class EvaluationError extends RuntimeError
{
    public function __construct(string $message = '', TokensStack $tokensStack = null)
    {
        if ($tokensStack instanceof TokensStack) {
            $message = $message . ' near ' . $tokensStack->head()->getValue();
        }
        parent::__construct($message);
    }
}