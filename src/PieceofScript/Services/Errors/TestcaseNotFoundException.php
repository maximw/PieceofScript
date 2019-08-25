<?php


namespace PieceofScript\Services\Errors;


class TestcaseNotFoundException extends \Exception
{
    public function __construct(string $testcaseName = "")
    {
        parent::__construct('Testcase "' . $testcaseName . '" not found');
    }
}