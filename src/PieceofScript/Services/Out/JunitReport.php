<?php


namespace PieceofScript\Services\Out;


use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Errors\InternalError;
use PieceofScript\Services\Statistics\Statistics;

class JunitReport
{

    /** @var string */
    protected $reportFile;

    /** @var Statistics */
    protected $statistics;

    /** @var string */
    protected $startFile;



    /** @var \SimpleXMLElement */
    protected $report;

    public function __construct(
        string $reportFile,
        Statistics $statistics,
        string $startFile
    )
    {
        $this->reportFile = realpath($reportFile);
        $this->statistics = $statistics;
        $this->startFile = $startFile;
    }

    public function generate()
    {
        Out::printDebug('Start generating report.');
        $this->report = new \SimpleXMLElement('testsuites');
        $testsuite = $this->report->addChild('testsuite');
        $testsuite->addAttribute('name', $this->startFile);

        $this->addProperties($testsuite);
        $this->addTestcases($testsuite);

        if (false === file_put_contents($this->reportFile, $this->report->asXML())) {
            throw new InternalError('Cannot write repoer file "' . $this->reportFile . '"');
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
        $property->addAttribute('value', $this->startFile);
        $this->report->addChild('properties');
    }

    protected function addConfigProperties(\SimpleXMLElement $properties)
    {
        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.endpoints_file');
        $property->addAttribute('value', Config::get()->getEndpointsFile());

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.endpoints_dir');
        $property->addAttribute('value', Config::get()->getEndpointsDir());

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.generators_file');
        $property->addAttribute('value', Config::get()->getGeneratorsFile());

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.generators_dir');
        $property->addAttribute('value', Config::get()->getGeneratorsDir());

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.cache_dir');
        $property->addAttribute('value', Config::get()->getCacheDir());

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.http_connect_timeout');
        $property->addAttribute('value', Config::get()->getHttpConnectTimeout());

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.http_read_timeout');
        $property->addAttribute('value', Config::get()->getHttpReadTimeout());

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.http_max_redirects');
        $property->addAttribute('value', Config::get()->getHttpMaxRedirects());

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.current_timestamp');
        $property->addAttribute('value', Config::get()->getCurrentTimestamp());

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.default_date_format');
        $property->addAttribute('value', Config::get()->getDefaultDateFormat());

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.default_timezone');
        $property->addAttribute('value', Config::get()->getDefaultTimezone());

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.json_max_depth');
        $property->addAttribute('value', Config::get()->getJsonMaxDepth());

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.random_seed');
        $property->addAttribute('value', Config::get()->getRandomSeed());

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'config.faker_locale');
        $property->addAttribute('value', Config::get()->getFakerLocale());
    }

    protected function addSystemProperties(\SimpleXMLElement $properties)
    {
        $property = $properties->addChild('property');
        $property->addAttribute('name', 'user.name');
        $property->addAttribute('value', get_current_user());

        $property = $properties->addChild('property');
        $property->addAttribute('name', 'user.home');
        $property->addAttribute('value', $_SERVER['HOME'] ?? (($_SERVER['HOMEDRIVE'] ?? '') . ($_SERVER['HOMEPATH'] ?? '')));
    }

    protected function addTestcases(\SimpleXMLElement $testsuite)
    {

    }

}