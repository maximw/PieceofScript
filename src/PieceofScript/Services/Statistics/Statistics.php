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

    /** @var int  */
    protected $totalEndpoints = 0;

    public function __construct(int $totalEndpoints = 0)
    {
        $this->totalEndpoints = $totalEndpoints;
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

    public function getStatistics()
    {
        return $this->statEndpoints;
    }

    public function printStatistics()
    {
        Out::printStatistics('Statistics:');

        $count['endpoints']['total'] = 0;
        $count['endpoints']['success'] = 0;
        $count['endpoints']['fail'] = 0;
        $count['calls']['total'] = 0;
        $count['calls']['success'] = 0;
        $count['calls']['fail'] = 0;        
        $count['assertions']['total'] = 0;
        $count['assertions']['success'] = 0;
        $count['assertions']['fail'] = 0;
        foreach ($this->statEndpoints as $endpointName => $endpointCalls) {
            $count['endpoints']['total']++;
            $successEndpoint = null;
            foreach ($endpointCalls->getCalls() as $call) {
                $successEndpoint = $successEndpoint === null ? true : ($successEndpoint && true);
                $count['calls']['total']++;
                $successCall = null;
                foreach ($call->getAssertions() as $assertion) {
                    $successCall = $successCall === null ? true : ($successCall && true);
                    $count['assertions']['total']++;
                    if ($assertion->getStatus()) {
                        $count['assertions']['success']++;
                    } else {
                        $count['assertions']['fail']++;
                        $successEndpoint = false;
                        $successCall = false;
                    }
                }
                if ($successCall) {
                    $count['calls']['success']++;
                } else {
                    $count['calls']['fail']++;
                }
            }
            if ($successEndpoint) {
                $count['endpoints']['success']++;
            } else {
                $count['endpoints']['fail']++;
            }
        }

        Out::printStatistics('Endpoints: ', 1);
        Out::printStatistics('total: '. $this->totalEndpoints, 2);
        Out::printStatistics('tested: '. $count['endpoints']['total'] . $this->formatPercent($count['endpoints']['total'], $this->totalEndpoints), 2);
        Out::printStatistics('success: '. $count['endpoints']['success'] . $this->formatPercent($count['endpoints']['success'], $this->totalEndpoints), 2);
        Out::printStatistics('fail: '. $count['endpoints']['fail'] . $this->formatPercent($count['endpoints']['fail'], $this->totalEndpoints), 2);

        Out::printStatistics('Endpoint calls: ', 1);
        Out::printStatistics('total: '. $count['calls']['total'], 2);
        Out::printStatistics('success: '. $count['calls']['success'] . $this->formatPercent($count['calls']['success'], $count['calls']['total']), 2);
        Out::printStatistics('fail: '. $count['calls']['fail'] . $this->formatPercent($count['calls']['fail'], $count['calls']['total']), 2);

        Out::printStatistics('Assertions: ', 1);
        Out::printStatistics('total: '. $count['assertions']['total'], 2);
        Out::printStatistics('success: '. $count['assertions']['success'] . $this->formatPercent($count['assertions']['success'], $count['assertions']['total']), 2);
        Out::printStatistics('fail: '. $count['assertions']['fail'] . $this->formatPercent($count['assertions']['fail'], $count['assertions']['total']), 2);
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