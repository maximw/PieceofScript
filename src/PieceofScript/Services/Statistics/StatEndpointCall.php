<?php


namespace PieceofScript\Services\Statistics;



class StatEndpointCall
{
    protected $file;

    protected $line;

    protected $startDate;

    protected $endDate;

    /** @var array */
    protected $request;

    /** @var array */
    protected $response;

    /** @var StatAssertion[] */
    protected $assertions;

    public function __construct(
        string $file,
        string $line
    )
    {
        $this->file = $file;
        $this->line = $line;
        $this->startDate = microtime(true);
    }

    public function end()
    {
        $this->endDate = microtime(true);
    }

    public function addAssertion(string $code, string $file, int $line, bool $success): self
    {
        $assertion = new StatAssertion($code);
        $this->assertions[] = $assertion;
        return $this;
    }

}