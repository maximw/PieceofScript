<?php


namespace PieceofScript\Services\Out;


use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Errors\InternalError;
use PieceofScript\Services\Statistics\StatAssertion;
use PieceofScript\Services\Statistics\StatEndpoint;
use PieceofScript\Services\Statistics\StatEndpointCall;
use PieceofScript\Services\Statistics\Statistics;

class JunitReport
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

    public function generate()
    {
        Out::printDebug('Start generating report.');
        $report = new \SimpleXMLElement('<testsuites></testsuites>');
        $report->addAttribute('name', $this->xmlEscape($this->startFile));
        $report->addAttribute('tests', $this->statistics->endpointsTested);
        $report->addAttribute('failures', $this->statistics->endpointsFailed);

        $testsuite = $report->addChild('testsuite');
        $this->addProperties($testsuite);
        $this->addTestcases($testsuite);
        $testsuite->addAttribute('name', $this->xmlEscape($this->startFile));
        $testsuite->addAttribute('tests', $this->statistics->endpointsTested);
        $testsuite->addAttribute('failures', $this->statistics->endpointsFailed);

        if (false === file_put_contents($this->reportFile, $report->asXML())) {
            throw new InternalError('Cannot write report file "' . $this->reportFile . '"');
        }
        Out::printDebug('Report was generated.');
    }


    protected function addProperties(\SimpleXMLElement $testsuite)
    {
        $properties = $testsuite->addChild('properties');

        $this->addConfigProperties($properties);
        $this->addSystemProperties($properties);

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'start_file');
        $property->addAttribute('value', $this->xmlEscape($this->startFile));
    }

    protected function addConfigProperties(\SimpleXMLElement $properties)
    {
        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.endpoints_file');
        $property->addAttribute('value', $this->xmlEscape(Config::get()->getEndpointsFile()));

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.endpoints_dir');
        $property->addAttribute('value', $this->xmlEscape(Config::get()->getEndpointsDir()));

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.generators_file');
        $property->addAttribute('value', $this->xmlEscape(Config::get()->getGeneratorsFile()));

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.generators_dir');
        $property->addAttribute('value', $this->xmlEscape(Config::get()->getGeneratorsDir()));

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.cache_dir');
        $property->addAttribute('value', $this->xmlEscape(Config::get()->getCacheDir()));

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.http_connect_timeout');
        $property->addAttribute('value', $this->xmlEscape(Config::get()->getHttpConnectTimeout()));

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.http_read_timeout');
        $property->addAttribute('value', $this->xmlEscape(Config::get()->getHttpTimeout()));

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.http_max_redirects');
        $property->addAttribute('value', $this->xmlEscape(Config::get()->getHttpMaxRedirects()));

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.current_timestamp');
        $property->addAttribute('value', $this->xmlEscape(Config::get()->getCurrentTimestamp()));

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.default_date_format');
        $property->addAttribute('value', $this->xmlEscape(Config::get()->getDefaultDateFormat()));

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.default_timezone');
        $property->addAttribute('value', $this->xmlEscape(Config::get()->getDefaultTimezone()->getName()));

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.json_max_depth');
        $property->addAttribute('value', $this->xmlEscape(Config::get()->getJsonMaxDepth()));

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.random_seed');
        $property->addAttribute('value', $this->xmlEscape(Config::get()->getRandomSeed()));

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.faker_locale');
        $property->addAttribute('value', $this->xmlEscape(Config::get()->getFakerLocale()));
    }

    protected function addSystemProperties(\SimpleXMLElement $properties)
    {
        $property = $properties->addChild('property');
        $property->addAttribute('name', 'user.name');
        $property->addAttribute('value', $this->xmlEscape(get_current_user()));

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'user.home');
        $property->addAttribute('value', $this->xmlEscape($_SERVER['HOME'] ?? (($_SERVER['HOMEDRIVE'] ?? '') . ($_SERVER['HOMEPATH'] ?? ''))));
    }

    protected function addTestcases(\SimpleXMLElement $testsuite)
    {
        /** @var StatEndpoint $statEndpoint */
        foreach ($this->statistics->getStatistics() as $statEndpoint) {
            /** @var StatEndpointCall $statEndpointCall */
            foreach ($statEndpoint->getCalls() as $statEndpointCall) {
                $testcase = $testsuite->addChild('testcase');
                $testcase->addAttribute('name', $this->xmlEscape($statEndpointCall->getCode()));
                $testcase->addAttribute('classname', $this->xmlEscape($statEndpoint->getEndpoint()->getOriginalName()));
                $testcase->addAttribute('assertions', $statEndpointCall->countAssertions());

                if (!$statEndpointCall->getStatus()) {
                    foreach ($statEndpointCall->getFailedAssertions() as $assertion) {
                        if (!$assertion->getStatus()) {
                            $failure = $testcase->addChild('failure', $this->xmlEscape($this->assertionToString($assertion)));
                        }
                    }
                }
                OutToString::printRequest($statEndpointCall->getRequest());
                OutToString::printResponse($statEndpointCall->getResponse());
                $testcase->addChild('system-out', $this->xmlEscape(OutToString::getBuffer()));
            }
        }
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

    protected function xmlEscape($string): string
    {
        return htmlspecialchars($string, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
}