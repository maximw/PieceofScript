<?php


namespace PieceofScript\Services\Errors\Parser;



class EmptyExpressionError extends ParserError
{
    public function __construct()
    {
        parent::__construct('Expression is empty');
    }
}