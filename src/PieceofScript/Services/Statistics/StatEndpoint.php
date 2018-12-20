<?php


namespace PieceofScript\Services\Statistics;


use PieceofScript\Services\Endpoints\Endpoint;

class StatEndpoint
{
    /** @var Endpoint */
    protected $endpoint;


    protected $calls;


    public function __construct($name, $file)
    {
        $this->name = $name;
        $this->file = $file;
        $this->startDate = microtime(true);
    }

    public function end()
    {
        $this->endDate = microtime(true);
    }

    public function addAssertion($code, $file, $success): self
    {
        $assertion = new StatAssertion($code);
        $this->assertions[] = $assertion;
        return $this;
    }

}