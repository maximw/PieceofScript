<?php

namespace PieceofScript\Services\Errors\InternalFunctionsErrors;

class ArgumentTypeError extends \Exception
{
    public function __construct(string $name, int $argumentNumber, string $typeGiven, $typeRequired = null)
    {
        if (null === $typeRequired) {
            parent::__construct('Internal function argument type error: ' . $name. '() does not take ' . ($argumentNumber + 1) . ' argument of type ' . $typeGiven);
        } else {
            if (is_array($typeRequired)) {
                parent::__construct('Internal function argument type error: ' . $name . '() requires ' . ($argumentNumber + 1) . ' argument of types ' . implode(', ', $typeRequired) . ', given ' . $typeGiven);
            } else {
                parent::__construct('Internal function argument type error: ' . $name . '() requires ' . ($argumentNumber + 1) . ' argument of type ' . $typeRequired . ', given ' . $typeGiven);
            }
        }
    }
}