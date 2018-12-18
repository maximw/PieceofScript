<?php


namespace PieceofScript\Services\Testcases;


class TestcaseCall
{

    /**
     * Called Test case
     * @var Testcase
     */
    public $testcase;

    /**
     *
     * @var array
     */
    public $parameters = [];

    public function __construct(Testcase $testcase, array $parameters = [])
    {
        $this->testcase = $testcase;
        $this->parameters = $parameters;
    }

}