<?php


namespace PieceofScript\Services\Statistics;



use function DeepCopy\deep_copy;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Variables\VariablesRepository;

class StatEndpointCall
{
    protected $code;

    protected $file;

    protected $line;

    protected $startDate;

    protected $endDate;

    protected $status = true;

    /** @var array */
    protected $request;

    /** @var array */
    protected $response;

    /** @var StatAssertion[] */
    protected $assertions = [];

    public function __construct(
        string $code,
        string $file,
        int $line,
        BaseLiteral $request,
        BaseLiteral $response
    )
    {
        $this->code = trim($code);
        $this->file = $file;
        $this->line = $line;
        $this->startDate = microtime(true);
        $this->setRequest($request);
        $this->setResponse($response);
    }

    public function end()
    {
        $this->endDate = microtime(true);
    }

    public function addAssertion(
        string $code,
        string $file,
        int $line,
        bool $status,
        VariablesRepository $variablesDump,
        array $usedVariables,
        $message
    ): self
    {
        $assertion = new StatAssertion($code, $file, $line, $status, $variablesDump, $usedVariables, $message);
        $this->assertions[] = $assertion;
        $this->status = $this->status && $assertion->getStatus();
        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    /**
     * @return StatAssertion[]
     */
    public function getAssertions(): array
    {
        return $this->assertions;
    }

    /**
     * @return StatAssertion[]
     */
    public function getSuccessAssertions(): array
    {
        return array_filter($this->assertions, function ($assertion) {return $assertion->getStatus();} );
    }

    /**
     * @return StatAssertion[]
     */
    public function getFailedAssertions(): array
    {
        return array_filter($this->assertions, function ($assertion) {return !$assertion->getStatus();} );
    }

    public function countAssertions(): int
    {
        return count($this->assertions);
    }

    public function countSuccessAssertions(): int
    {
        return count($this->getSuccessAssertions());
    }

    public function countFailedAssertions(): int
    {
        return count($this->getFailedAssertions());
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest(ArrayLiteral $request)
    {
        $this->request = deep_copy($request);
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(ArrayLiteral $response)
    {
        $this->response = deep_copy($response);
    }
}