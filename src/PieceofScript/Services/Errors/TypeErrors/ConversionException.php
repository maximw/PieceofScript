<?php


namespace PieceofScript\Services\Errors\TypeErrors;


use PieceofScript\Services\Errors\RuntimeError;

class ConversionException extends RuntimeError
{
    public function __construct(string $typeFrom, string $typeTo)
    {
        parent::__construct('Cannot convert ' . $typeFrom . ' to ' . $typeTo);
    }

}