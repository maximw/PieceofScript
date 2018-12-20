<?php


namespace PieceofScript\Services\Statistics;


use PieceofScript\Services\Contexts\ContextStack;
use PieceofScript\Services\Endpoints\EndpointCall;
use PieceofScript\Services\Out\Out;

class Statistics
{
    protected $statEndpoints = [];

    protected $currentEndpointCall;

    public function __construct()
    {
    }

    public function addCall(EndpointCall $call, ContextStack $contextStack, array $request, array $response)
    {
        $endPoint = $call->getEndpoint();

        if (!array_key_exists($endPoint->getName(), $this->statEndpoints)) {
            $this->statEndpoints[$endPoint->getName()] = [];
        }

        if ($this->currentEndpointCall instanceof StatEndpointCall) {
            $this->currentEndpointCall->end();
        }

        $newCall = new StatEndpointCall();

        $this->statEndpoints[$endPoint->getName()] = $newCall;
        $this->currentEndpointCall = $newCall;
    }




    public function addAssertion(
        string $code,
        bool $status,
        ContextStack $contextStack
    )
    {
        if ($this->currentEndpointCall instanceof StatEndpointCall) {
            $this->currentEndpointCall->addAssertion($code, $contextStack->head()->getFile(), $contextStack->head()->getLine(), $status);
        } else {
            Out::printWarning('Assertion outside of Endpoint call "' . $code . '" ', $contextStack);
        }

    }
}