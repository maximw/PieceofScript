<?php


namespace PieceofScript\Services\Statistics;


use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Contexts\ContextStack;
use PieceofScript\Services\Endpoints\Endpoint;
use PieceofScript\Services\Endpoints\EndpointsRepository;
use PieceofScript\Services\Out\Out;
use PieceofScript\Services\Values\ArrayLiteral;

class Statistics
{
    /** @var StatEndpoint[]  */
    protected $statEndpoints = [];

    /** @var StatEndpointCall */
    protected $currentEndpointCall;

    /** @var StatAssertion[] */
    protected $globalAssertions = [];

    /** @var int  */
    public $endpointsTotal = 0;
    public $endpointsTested = 0;
    public $endpointsSuccess = 0;
    public $endpointsFailed = 0;

    public $callsTotal = 0;
    public $callsSuccess = 0;
    public $callsFailed = 0;

    public $assertsTotal = 0;
    public $assertsSuccess = 0;
    public $assertsFailed = 0;

    public function __construct(EndpointsRepository $endpointsRepository)
    {
        $this->endpointsTotal = $endpointsRepository->getCount();
        foreach ($endpointsRepository->getAll() as $endpoint) {
            $this->addEndpoint($endpoint);
        }
    }

    public function addEndpoint(Endpoint $endPoint)
    {
        if (!array_key_exists($endPoint->getDefinition()->getOriginalString(), $this->statEndpoints)) {
            $this->statEndpoints[$endPoint->getDefinition()->getOriginalString()] = new StatEndpoint($endPoint);
        }
    }

    public function addCall(
        string $code,
        Endpoint $endPoint,
        ContextStack $contextStack,
        ArrayLiteral $request,
        ArrayLiteral $response
    )
    {
        $this->addEndpoint($endPoint);

        if ($this->currentEndpointCall instanceof StatEndpointCall) {
            $this->endCurrentCall();
        }

        $newCall = new StatEndpointCall(
            $code,
            $contextStack->neck()->getFile(),
            $contextStack->neck()->getLine(),
            $request,
            $response
        );

        $this->statEndpoints[$endPoint->getDefinition()->getOriginalString()]->addCall($newCall);
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
        ?bool $status,
        ContextStack $contextStack,
        array $usedVariables,
        string $message
    )
    {
        if ($this->currentEndpointCall instanceof StatEndpointCall) {
            $this->currentEndpointCall->addAssertion(
                $code,
                $contextStack->head()->getFile(),
                $contextStack->head()->getLine(),
                $status,
                $contextStack->head()->dumpVariables(),
                $usedVariables,
                $message
            );
        } elseif (!Config::get()->getSkipAssertions()){
            $assertion = new StatAssertion(
                $code,
                $contextStack->head()->getFile(),
                $contextStack->head()->getLine(),
                $status,
                $contextStack->head()->dumpVariables(),
                $usedVariables,
                $message
            );
            $this->globalAssertions[] = $assertion;
        } else {
            Out::printWarning('Skipped assertion outside of Endpoint call "' . $code . '" ', $contextStack);
        }

    }

    public function getStatistics()
    {
        return $this->statEndpoints;
    }

    public function prepareStatistics()
    {
        foreach ($this->statEndpoints as $endpointName => $endpointCalls) {
            $successEndpoint = null;
            foreach ($endpointCalls->getCalls() as $call) {
                $successEndpoint = $successEndpoint === null ? true : ($successEndpoint && true);
                $this->callsTotal++;
                $successCall = null;
                foreach ($call->getAssertions() as $assertion) {
                    $successCall = $successCall === null ? true : ($successCall && true);
                    $this->assertsTotal++;
                    if ($assertion->getStatus() === true) {
                        $this->assertsSuccess++;
                    } elseif ($assertion->getStatus() === false) {
                        $this->assertsFailed++;
                        $successEndpoint = false;
                        $successCall = false;
                    }
                }
                if ($successCall) {
                    $this->callsSuccess++;
                } else {
                    $this->callsFailed++;
                }
            }
            if ($successEndpoint === true) {
                $this->endpointsSuccess++;
                $this->endpointsTested++;
            } elseif ($successEndpoint === false) {
                $this->endpointsFailed++;
                $this->endpointsTested++;
            }
        }

        if (!Config::get()->getSkipAssertions()) {
            foreach ($this->globalAssertions as $assertion) {
                $this->assertsTotal++;
                if ($assertion->getStatus()) {
                    $this->assertsSuccess++;
                } else {
                    $this->assertsFailed++;
                }
            }
        }
    }

    public function printStatistics()
    {
        Out::printStatistics('Statistics:');

        Out::printStatistics('Endpoints: ', 1);
        Out::printStatistics('total: '. $this->endpointsTotal, 2);
        Out::printStatistics('tested: '. $this->endpointsTested . $this->formatPercent($this->endpointsTested, $this->endpointsTotal), 2);
        Out::printStatistics('success: '. $this->endpointsSuccess . $this->formatPercent($this->endpointsSuccess, $this->endpointsTotal), 2);
        Out::printStatistics('fail: '. $this->endpointsFailed . $this->formatPercent($this->endpointsFailed, $this->endpointsTotal), 2);

        Out::printStatistics('Endpoint calls: ', 1);
        Out::printStatistics('total: '. $this->callsTotal, 2);
        Out::printStatistics('success: '. $this->callsSuccess . $this->formatPercent($this->callsSuccess, $this->callsTotal), 2);
        Out::printStatistics('fail: '. $this->callsFailed . $this->formatPercent($this->callsFailed, $this->callsTotal), 2);

        Out::printStatistics('Assertions: ', 1);
        Out::printStatistics('total: '. $this->assertsTotal, 2);
        Out::printStatistics('success: '. $this->assertsSuccess . $this->formatPercent($this->assertsSuccess, $this->assertsTotal), 2);
        Out::printStatistics('fail: '. $this->assertsFailed . $this->formatPercent($this->assertsFailed, $this->assertsTotal), 2);
    }

    protected function formatPercent(int $count, int $total)
    {
        $percent = 0;
        if ($total != 0) {
            $percent = $count / $total * 100;
        }
        return ' (' . round($percent, 1) . '%)';
    }

}