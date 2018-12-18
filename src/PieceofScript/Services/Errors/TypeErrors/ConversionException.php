<?php


namespace PieceofScript\Services\Errors\TypeErrors;


class ConversionException extends \Exception
{
    public function __construct(string $typeFrom, string $typeTo)
    {
        parent::__construct('Cannot convert ' . $typeFrom . ' to ' . $typeTo);
    }

}