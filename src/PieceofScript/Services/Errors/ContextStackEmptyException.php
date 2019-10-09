<?php


namespace PieceofScript\Services\Errors;


class ContextStackEmptyException extends RuntimeError
{
    public function __construct()
    {
        parent::__construct('Something went wrong. Context stack is empty.');
    }
}