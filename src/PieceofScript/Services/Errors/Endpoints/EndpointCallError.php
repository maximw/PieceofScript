<?php


namespace PieceofScript\Services\Errors\Endpoints;


use PieceofScript\Services\Contexts\ContextStack;
use PieceofScript\Services\Endpoints\Endpoint;
use PieceofScript\Services\Errors\RuntimeError;

class EndpointCallError extends RuntimeError
{
    public function __construct(Endpoint $endpoint, string $message, ContextStack $contextStack)
    {
        $message = $message . 'Cannot call endpoint "' . $endpoint->getOriginalName() . '": ' . $message;
        parent::__construct($message, $contextStack);
    }
}