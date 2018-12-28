<?php


namespace PieceofScript\Services\Errors;

use PieceofScript\Services\Contexts\ContextStack;

/**
 * Internal interpretation error during running scenario. Causes print ContextStack
 *
 * Class RuntimeError
 */
class RuntimeError extends InternalError
{
    /** @var ContextStack */
    public $contextStack;

    public function __construct(string $message = "", ContextStack $contextStack = null)
    {
        parent::__construct($message);
        $this->contextStack = $contextStack;
    }
}