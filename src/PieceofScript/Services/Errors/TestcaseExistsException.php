<?php


namespace PieceofScript\Services\Errors;


use PieceofScript\Services\Testcases\Testcase;

class TestcaseExistsException extends \Exception
{
    public function __construct(string $testcaseName, Testcase $testcase)
    {
        parent::__construct('Testcase already exists "' . $testcaseName . '" as "' . $testcase->originalName . '"');
    }
}