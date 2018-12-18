<?php


namespace PieceofScript\Services\Statistics;


class Statistics
{
    protected $testSuites;

    public function __construct(string $startFile)
    {
        $this->testSuites = new StatTestSuites($startFile);
    }

    public function addTestSuite(string $name, string $file)
    {
        $this->testSuites->addTestSuite($name, $file);
    }

    public function addTestCase(): StatTestSuite
    {
        return $this->testSuites->getCurrentTestSuite()->addTestCase();
    }

    public function endTestCase()
    {
        $this->testSuites->getCurrentTestSuite()->getCurrentTestCase()->end();
    }

    public function addAssertion()
    {
        $this->testSuites->getCurrentTestSuite()->getCurrentTestCase()->addAssertion();
    }
}