<?php


namespace PieceofScript\Services\Statistics;


use PieceofScript\Services\Contexts\ContextStack;
use PieceofScript\Services\Endpoints\EndpointCall;
use PieceofScript\Services\Out\Out;
use PieceofScript\Services\Values\ArrayLiteral;

class Statistics
{
    /** @var StatEndpoint[]  */
    protected $statEndpoints = [];

    /** @var StatEndpointCall */
    protected $currentEndpointCall;

    public function __construct()
    {
    }

    public function addCall(EndpointCall $call, ContextStack $contextStack, ArrayLiteral $request, ArrayLiteral $response)
    {
        $endPoint = $call->getEndpoint();

        if (!array_key_exists($endPoint->getName(), $this->statEndpoints)) {
            $this->statEndpoints[$endPoint->getName()] = new StatEndpoint($endPoint);
        }

        if ($this->currentEndpointCall instanceof StatEndpointCall) {
            $this->endCurrentCall();
        }

        $newCall = new StatEndpointCall($contextStack->neck()->getFile(), $contextStack->neck()->getLine(), $request, $response);

        $this->statEndpoints[$endPoint->getName()]->addCall($newCall);
        $this->currentEndpointCall = $newCall;
    }

    public function setRequest(ArrayLiteral $request)
    {
        if ($this->currentEndpointCall instanceof StatEndpointCall) {
            $this->currentEndpointCall->setRequest($request);
        }
    }

    public function setResponse(ArrayLiteral $response)
    {
        if ($this->currentEndpointCall instanceof StatEndpointCall) {
            $this->currentEndpointCall->setResponse($response);
        }
    }

    public function endCurrentCall()
    {
        if ($this->currentEndpointCall instanceof StatEndpointCall) {
            $this->currentEndpointCall->end();
            $this->currentEndpointCall = null;
        }
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
            Out::printWarning('Skipped assertion outside of Endpoint call "' . $code . '" ', $contextStack);
        }

    }
}