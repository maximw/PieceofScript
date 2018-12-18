<?php


namespace PieceofScript\Services\Statistics;


class StatTestSuites
{
    protected $date;

    protected $systemUser;

    protected $testSuites;

    protected $defaultTestSuite;

    public function __construct($startFile)
    {
        $this->date = new \DateTime();
        $this->systemUser = get_current_user();

        $this->defaultTestSuite = new StatTestSuite('default', $startFile);
        $this->defaultTestSuite->addTestCase('default', $startFile);
    }

    public function addTestSuite(string $name, string $file): StatTestSuites
    {
        $testSuite = new StatTestSuite($name, $file);
        array_push($this->testSuites, $testSuite);
        return $this;
    }

    public function getCurrentTestSuite(): StatTestSuite
    {
        if (count($this->testSuites) > 0) {
            return end($this->testSuites);
        }
        return $this->defaultTestSuite;
    }
}