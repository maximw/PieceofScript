<?php


namespace PieceofScript\Services\Statistics;


use PieceofScript\Services\Endpoints\Endpoint;

class StatEndpoint
{
    /** @var Endpoint */
    protected $endpoint;

    /** @var StatEndpointCall[]  */
    protected $calls = [];

    public function __construct(Endpoint $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function addCall(StatEndpointCall $statEndpointCall)
    {
        $this->calls[] = $statEndpointCall;
    }

    /**
     * @return StatEndpointCall[]
     */
    public function getCalls(): array
    {
        return $this->calls;
    }

    public function getEndpoint(): Endpoint
    {
        return $this->endpoint;
    }
}