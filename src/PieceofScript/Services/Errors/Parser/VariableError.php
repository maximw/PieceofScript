<?php


namespace PieceofScript\Services\Errors\Parser;


use PieceofScript\Services\Errors\RuntimeError;
use PieceofScript\Services\Values\VariableName;

class VariableError extends RuntimeError
{
    public function __construct(VariableName $variableName, string $message = "")
    {
        $message = ((string) $variableName) . ': '. $message;
        parent::__construct($message);
    }


}