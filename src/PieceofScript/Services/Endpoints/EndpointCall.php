<?php


namespace PieceofScript\Services\Endpoints;


use PieceofScript\Services\Call\BaseCall;

class EndpointCall extends BaseCall
{
    /**
     * Called Endpoint
     * @var Endpoint
     */
    protected $endpoint;

    /**
     * Given parameters
     *
     * @var array
     */
    protected $parameters = [];

    public function __construct(Endpoint $endpoint, array $parameters)
    {
        $this->endpoint = $endpoint;
        $this->parameters = $parameters;
    }

    /**
     * @return Endpoint
     */
    public function getEndpoint(): Endpoint
    {
        return $this->endpoint;
    }

    /**
     * @param Endpoint $endpoint
     * @return EndpointCall
     */
    public function setEndpoint(Endpoint $endpoint): EndpointCall
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return EndpointCall
     */
    public function setParameters(array $parameters): EndpointCall
    {
        $this->parameters = $parameters;
        return $this;
    }



}