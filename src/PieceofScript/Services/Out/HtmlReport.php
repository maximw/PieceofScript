<?php


namespace PieceofScript\Services\Out;


use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Errors\InternalError;
use PieceofScript\Services\Statistics\StatAssertion;
use PieceofScript\Services\Statistics\Statistics;


class HtmlReport
{
    /** @var string */
    protected $reportFile;

    /** @var Statistics */
    protected $statistics;

    /** @var string */
    protected $startFile;

    public function __construct(
        string $reportFile,
        Statistics $statistics,
        string $startFile
    )
    {
        $this->reportFile = $reportFile;
        $this->statistics = $statistics;
        $this->startFile = $startFile;
    }

    /**
     * @throws InternalError
     */
    public function generate()
    {
        Out::printDebug('Start generating HTML report.');
        $view = new View();

        $report = $view->render('htmlReport', [
            'pieEndpoints' => $this->pieEndpoints(),
            'pieAssertions' => $this->pieAssertions(),
            'tableEndpoints' => $this->tableEndpoints(),
            'listEndpoints' => $this->listEndpoints(),
            'configProperties' => $this->configProperties(),
            'systemProperties' => $this->systemProperties(),
            'consoleOutput' => $this->consoleOutput(),
        ]);
        if (false === file_put_contents($this->reportFile, $report)) {
            throw new InternalError('Cannot write report file "' . $this->reportFile . '"');
        }
        Out::printDebug('HTML report has been generated.');
    }

    /**
     * @return string
     * @throws InternalError
     */
    protected function pieEndpoints(): string
    {
        $view = new View();
        return $view->render('pieEndpoints', [
            'total' => $this->statistics->endpointsTotal,
            'success' => $this->statistics->endpointsSuccess,
            'failed' => $this->statistics->endpointsFailed,
            'not_tested' => $this->statistics->endpointsTotal - $this->statistics->endpointsSuccess - $this->statistics->endpointsFailed,
        ]);
    }

    /**
     * @return string
     * @throws InternalError
     */
    protected function pieAssertions(): string
    {
        $view = new View();
        return $view->render('pieAssertions', [
            'total' => $this->statistics->assertsTotal,
            'success' => $this->statistics->assertsSuccess,
            'failed' => $this->statistics->assertsFailed,
        ]);
    }

    /**
     * @return string
     * @throws InternalError
     */
    protected function tableEndpoints(): string
    {
        $view = new View();
        return $view->render('tableEndpoints', [
            'stat' => $this->statistics,
        ]);
    }

    /**
     * @return string
     * @throws InternalError
     */
    protected function listEndpoints(): string
    {
        $view = new View();
        return $view->render('listEndpoints', [
            'stat' => $this->statistics,
        ]);
    }

    /**
     * @return string
     * @throws InternalError
     */
    protected function configProperties(): string
    {
        $properties = Config::get()->export();
        foreach ($properties as $key => $property) {
            if (is_bool($property)) {
                $properties[$key] = $property ? 'true' : 'false';
            } elseif ($property instanceof \DateTimeZone) {
                $properties[$key] = $property->getName();
            }
        }

        $view = new View();
        return $view->render('configProperties', [
            'config' => $properties,
        ]);
    }

    /**
     * @return string
     * @throws InternalError
     */
    protected function systemProperties(): string
    {
        $view = new View();
        return $view->render('systemProperties', [
            'properties' => [
                'Start file' => $this->startFile,
                'Working dir' => getcwd(),
                'User name' => get_current_user(),
                'Home directory' => $_SERVER['HOME'] ?? (($_SERVER['HOMEDRIVE'] ?? '') . ($_SERVER['HOMEPATH'] ?? '')),
            ],
        ]);

    }

    /**
     * @return string
     * @throws InternalError
     */
    protected function consoleOutput(): string
    {
        $view = new View();
        return $view->render('consoleOutput', [
            'output' => '',//$this->statistics->getOutput(),
        ]);
    }


    protected function assertionToString(StatAssertion $assertion): string
    {
        $result = '';
        $result .= $assertion->getCode() . PHP_EOL;
        $result .= $assertion->getFile() . ' line ' . $assertion->getLine() . PHP_EOL;
        $result .= $assertion->getMessage() . PHP_EOL;

        $usedVariables = $assertion->getUsedVariables();
        if (count($assertion->getUsedVariables())) {
            $result .= 'Variables dump:' . PHP_EOL;
            foreach ($usedVariables as $varName) {
                if ($assertion->getVariables()->exists($varName, false)) {
                    OutToString::printValues([$assertion->getVariables()->get($varName)->toPrint()]);
                    $value = ' = ' . OutToString::getBuffer();
                } else {
                    $value = ' - not found';
                }
                $result .= '$' . $varName->name . $value . PHP_EOL;
            }
        }
        return $result;
    }





}