<?php


namespace PieceofScript\Services\Statistics;



use function DeepCopy\deep_copy;
use PieceofScript\Services\Values\ArrayLiteral;

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
        string $line,
        ArrayLiteral $request,
        ArrayLiteral $response
    )
    {
        $this->file = $file;
        $this->line = $line;
        $this->startDate = microtime(true);
        $this->setRequest($request);
        $this->setResponse($response);
    }

    public function setRequest(ArrayLiteral $request)
    {
        $this->request = deep_copy($request);
    }

    public function setResponse(ArrayLiteral $response)
    {
        $this->response = deep_copy($response);
    }

    public function end()
    {
        $this->endDate = microtime(true);
    }

    public function addAssertion(string $code, string $file, int $line, bool $status): self
    {
        $assertion = new StatAssertion($code, $file, $line, $status);
        $this->assertions[] = $assertion;
        return $this;
    }

}