<?php


namespace PieceofScript\Services\Statistics;


class StatTestSuite
{
    protected $name;

    protected $file;

    protected $startDate;

    protected $endDate;

    protected $testCases;

    public function __construct(string $name, string $file)
    {
        $this->name = $name;
        $this->file = $file;
        $this->startDate = microtime(true);
    }

    public function getCurrentTestCase(): StatTestCase
    {
        return end($this->testCases);
    }

    public function end()
    {
        $this->endDate = microtime(true);
    }

    public function addTestCase(string $name, string $file): self
    {
        $testCase = new StatTestCase();
        $testCases[] = $testCase;
        return $this;
    }
}