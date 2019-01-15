<?php


namespace PieceofScript\Services\Errors\TypeErrors;

use PieceofScript\Services\Errors\RuntimeError;

class IncompatibleTypesOperationException extends RuntimeError
{
    public function __construct(string $operation, string $type1, string $type2 = null)
    {
        if (null === $type2) {
            $message = $operation . '  is not allowed operation with ' . $type1;
        } else {
            $message = $operation . '  is not allowed operation between ' . $type1 . ' and ' . $type2;
        }
        parent::__construct($message);
    }

}