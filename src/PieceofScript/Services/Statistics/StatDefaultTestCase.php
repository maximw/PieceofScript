<?php


namespace PieceofScript\Services\Statistics;


class StatTestCase
{
    protected $name;

    protected $file;

    protected $startDate;

    protected $endDate;

    protected $assertions;

    public function __construct($name, $file)
    {
        $this->name = $name;
        $this->file = $file;
        $this->startDate = microtime(true);
    }

    public function end()
    {
        $this->endDate = microtime(true);
    }

    public function addAssertion($code, $file, $success): self
    {
        $assertion = new StatAssertion($code);
        $this->assertions[] = $assertion;
        return $this;
    }

}