<?php

namespace PieceofScript\Services\Errors\InternalFunctionsErrors;

use PieceofScript\Services\Errors\RuntimeError;

class ArgumentsCountError extends RuntimeError
{
    public function __construct(string $name, int $countGiven, int $countRequired)
    {
        parent::__construct('Generator arguments count error: ' . $name. '() requires ' . $countRequired . ', given ' . $countGiven);
    }
}