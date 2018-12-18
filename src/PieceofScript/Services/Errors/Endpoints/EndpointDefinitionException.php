<?php


namespace PieceofScript\Services\Errors\Endpoints;


use PieceofScript\Services\Errors\InternalError;

class EndpointDefinitionException extends InternalError
{
    public function __construct(string $message, string $endpointName, string $fileName)
    {
        $message = $message . ' Endpoint "' . $endpointName . '" in ' . $fileName;
        parent::__construct($message);
    }
}