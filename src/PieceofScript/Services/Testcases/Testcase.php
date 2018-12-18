<?php


namespace PieceofScript\Services\Testcases;


class Testcase
{
    /**
     * Normalized test case name
     * @var string
     */
    public $name;

    /**
     * Original test case name
     * @var string
     */
    public $originalName;

    /**
     * List of arguments
     * @var array
     */
    public $arguments = [];

    /**
     * Test case body
     * @var array
     */
    public $lines = [];

    /**
     * File name where test case was declared
     * @var string
     */
    public $file;

    /**
     * Line number of declaration
     * @var int
     */
    public $lineNumber;

    public function __construct(string $name, string $originalName, string $file, int $lineNumber, array $lines = [])
    {
        $this->name = $name;
        $this->originalName = $originalName;
        $this->file = $file;
        $this->lineNumber = $lineNumber;
        $this->lines = $lines;
    }

    public function addLine(string $line): self
    {
        $this->lines[] = $line;
        return $this;
    }

    public function hasArguments():bool
    {
        return (bool) count($this->arguments);
    }
}