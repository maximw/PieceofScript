<?php

namespace PieceofScript\Services\Errors\InternalFunctionsErrors;

class ArgumentsCountError extends \Exception
{
    public function __construct(string $name, int $countGiven, int $countRequired)
    {
        parent::__construct('Internal function arguments count error: ' . $name. ' requires ' . $countRequired . ', given' . $countGiven);
    }
}