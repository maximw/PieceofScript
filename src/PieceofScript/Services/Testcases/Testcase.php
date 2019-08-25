<?php


namespace PieceofScript\Services\Testcases;


use PieceofScript\Services\Call\BaseCall;

class Testcase
{

    /**
     * @var BaseCall
     */
    protected $definition;

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

    public function __construct(BaseCall $definition, string $file, int $lineNumber, array $lines = [])
    {
        $this->definition = $definition;
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

    /**
     * @return BaseCall
     */
    public function getDefinition(): BaseCall
    {
        return $this->definition;
    }

    /**
     * @param BaseCall $definition
     * @return Testcase
     */
    public function setDefinition(BaseCall $definition): Testcase
    {
        $this->definition = $definition;
        return $this;
    }

    /**
     * @return array
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * @param array $lines
     * @return Testcase
     */
    public function setLines(array $lines): Testcase
    {
        $this->lines = $lines;
        return $this;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @param string $file
     * @return Testcase
     */
    public function setFile(string $file): Testcase
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return int
     */
    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    /**
     * @param int $lineNumber
     * @return Testcase
     */
    public function setLineNumber(int $lineNumber): Testcase
    {
        $this->lineNumber = $lineNumber;
        return $this;
    }

}