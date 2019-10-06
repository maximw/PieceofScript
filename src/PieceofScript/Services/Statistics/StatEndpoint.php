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

    /**
     * @return Endpoint
     */
    public function getEndpoint(): Endpoint
    {
        return $this->endpoint;
    }

    /**
     * @param StatEndpointCall $statEndpointCall
     * @return $this
     */
    public function addCall(StatEndpointCall $statEndpointCall): self
    {
        $this->calls[] = $statEndpointCall;
        return $this;
    }

    /**
     * @return StatEndpointCall[]
     */
    public function getCalls(): array
    {
        return $this->calls;
    }

    /**
     * @return StatEndpointCall[]
     */
    public function getSuccessCalls(): array
    {
        return array_filter($this->calls, function ($call) {return $call->isSuccess() === true;} );
    }

    /**
     * @return StatEndpointCall[]
     */
    public function getFailedCalls(): array
    {
        return array_filter($this->calls, function ($call) {return $call->isSuccess() === false;} );
    }


    /**
     * @return bool|null
     */
    public function isSuccess(): ?bool
    {
        $successCount = $this->countSuccessAssertions();
        $failedCount = $this->countFailedAssertions();
        if ($successCount === 0 && $failedCount === 0) {
            return null;
        }

        return $failedCount === 0;
    }

    /**
     * @return int
     */
    public function countCalls(): int
    {
        return count($this->getCalls());
    }

    /**
     * @return int
     */
    public function countSuccessCalls(): int
    {
        return count($this->getSuccessCalls());
    }

    /**
     * @return int
     */
    public function countFailedCalls(): int
    {
        return count($this->getFailedCalls());
    }

    /**
     * @return int
     */
    public function countAssertions(): int
    {
        $count = 0;
        foreach ($this->getCalls() as $call) {
            $count = $count + $call->countAssertions();
        }
        return $count;
    }

    /**
     * @return int
     */
    public function countSuccessAssertions(): int
    {
        $count = 0;
        foreach ($this->getCalls() as $call) {
            $count = $count + $call->countSuccessAssertions();
        }
        return $count;
    }

    /**
     * @return int
     */
    public function countFailedAssertions(): int
    {
        $count = 0;
        foreach ($this->getCalls() as $call) {
            $count = $count + $call->countFailedAssertions();
        }
        return $count;
    }
}