<?php


namespace PieceofScript\Services\Errors\Endpoints;


use PieceofScript\Services\Endpoints\Endpoint;
use PieceofScript\Services\Errors\RuntimeError;

class EndpointCallError extends RuntimeError
{
    public function __construct(Endpoint $endpoint, string $message)
    {
        $message = $message . 'Cannot call endpoint "' . $endpoint->getDefinition()->getOriginalString() . '": ' . $message;
        parent::__construct($message);
    }
}