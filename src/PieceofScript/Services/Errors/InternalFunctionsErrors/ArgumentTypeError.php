<?php

namespace PieceofScript\Services\Errors\InternalFunctionsErrors;

class ArgumentTypeError extends \Exception
{
    public function __construct(string $name, int $argumentNumber, string $typeGiven, string $typeRequired = null)
    {
        if (null === $typeRequired) {
            parent::__construct('Internal function argument type error: ' . $name. '() does not take ' . ($argumentNumber + 1) . ' argument of type ' . $typeGiven);
        } else {
            parent::__construct('Internal function argument type error: ' . $name. '() requires ' . ($argumentNumber + 1) . ' argument of type ' . $typeRequired . ', given ' . $typeGiven);
        }
    }
}