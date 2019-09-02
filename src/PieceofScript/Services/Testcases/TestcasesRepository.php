<?php


namespace PieceofScript\Services\Testcases;


use function DeepCopy\deep_copy;
use PieceofScript\Services\Call\BaseCall;
use PieceofScript\Services\Errors\TestcaseExistsException;
use PieceofScript\Services\Parsing\CallLexer;


class TestcasesRepository
{
    /** @var Testcase[] */
    protected $testcases = [];

    /** @var CallLexer */
    protected $callLexer;

    public function __construct(CallLexer $callLexer)
    {
        $this->callLexer = $callLexer;
    }

    /**
     * Returns Testcase by Call
     *
     * @param BaseCall $call
     * @return TestcaseCall|null
     * @throws \Exception
     */
    public function getByCall(BaseCall $call)
    {
        foreach ($this->testcases as $testcase) {
            if ($testcase->getDefinition()->isEqual($call)) {
                return deep_copy($testcase);
            }
        }

        return null;
    }

    /**
     * Get all Testcases without arguments for run with empty expression
     *
     * @return Testcase[]
     */
    public function getWithoutArguments(): array
    {
        $testcases = [];
        foreach ($this->testcases as $testcase) {
            if (count($testcase->getDefinition()->getArguments()) === 0) {
                $testcases[] = $testcase;
            }
        }
        return $testcases;
    }

    /**
     * Add new Testcase to repository
     *
     * @param string $testcaseName
     * @param string $file
     * @param int $lineNumber
     * @param array $lines
     * @return Testcase
     * @throws TestcaseExistsException
     */
    public function add(string $testcaseName, string $file, int $lineNumber, array $lines = []): Testcase
    {
        $definition = $this->callLexer->getCall($testcaseName);

        $testcase = $this->getByCall($definition);
        if ($testcase instanceof Testcase) {
            throw new TestcaseExistsException($testcaseName, $testcase);
        }

        $testcase = new Testcase($definition, $file, $lineNumber, $lines);

        $this->testcases[] = $testcase;
        return $testcase;
    }

}