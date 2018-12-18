<?php


namespace PieceofScript\Services\Testcases;


use PieceofScript\Services\Errors\TestcaseExistsException;
use PieceofScript\Services\Errors\TestcaseNotFoundException;
use PieceofScript\Services\Values\VariableName;

class TestcasesRepository
{
    const ARGUMENT_PLACEHOLDER = 'f132d59f3587463b99e3262a8b5a7975'; //random v4 UUID

    /** @var Testcase[] */
    protected $testcases = [];

    public function __construct()
    {
    }

    /**
     * Returns Testcase wrapped by TestcaseCall with given parameters when it is called by "run" command
     *
     * @param string $testcaseCallExpression
     * @return TestcaseCall
     * @throws \Exception
     */
    public function getByCall(string $testcaseCallExpression): TestcaseCall
    {
        foreach ($this->testcases as $testcaseName => $testcase) {
            $parameters = $this->match($testcaseCallExpression, $testcaseName);
            if (false !== $parameters) {
                $testcaseCall = new TestcaseCall($testcase, $parameters);
                return $testcaseCall;
            }
        }

        throw new \Exception('Testcase not found ' . $testcaseCallExpression);
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
    public function add(string $testcaseName, string $file, int $lineNumber, array $lines = [])
    {
        $normalizedName = $this->normalizeName($testcaseName);

        $testcase = $this->getByName($normalizedName);
        if ($testcase instanceof Testcase) {
            throw new TestcaseExistsException($testcaseName, $testcase);
        }

        $this->testcases[$normalizedName] = new Testcase($normalizedName, $testcaseName, $file, $lineNumber, $lines);
        $this->testcases[$normalizedName]->arguments = $this->extractArguments($testcaseName);
        return $this->testcases[$normalizedName];
    }

    /**
     * Add command line to Testcase body
     *
     * @param string $normalizedName
     * @param string $line
     * @throws TestcaseNotFoundException
     */
    public function addLine(string $normalizedName, string $line)
    {
        if (!$this->getByName($normalizedName) instanceof Testcase) {
            throw new TestcaseNotFoundException($normalizedName);
        }

        $this->testcases[$normalizedName]->addLine($line);
    }

    /**
     * Returns Testcase by name or null if not found
     *
     * @param string $normalizedName
     * @return null|Testcase
     */
    protected function getByName(string $normalizedName)
    {
        return $this->testcases[$normalizedName] ?? null;
    }

    /**
     * Normalize Testcase's name
     *
     * @param string $testcaseName
     * @return string
     * @throws \Exception
     */
    protected function normalizeName(string $testcaseName): string
    {
        if (strpos($testcaseName, self::ARGUMENT_PLACEHOLDER) !== false) {
            throw new \Exception(self::ARGUMENT_PLACEHOLDER.' is not allowed in test case definition');
        }
        $testcaseName = trim($testcaseName);
        $testcaseName = preg_replace('/\s\s+/i', ' ', $testcaseName);
        $testcaseName = preg_replace('/\s*(\$[a-z][a-z0-9_]*)\s*/i', self::ARGUMENT_PLACEHOLDER, $testcaseName);
        $testcaseName = strtolower($testcaseName);
        return $testcaseName;
    }

    /**
     * Returns arguments names from Testcase name
     *
     * @param string $testcaseName
     * @return array
     * @throws \Exception
     */
    protected function extractArguments(string $testcaseName): array
    {
        if (strpos($testcaseName, self::ARGUMENT_PLACEHOLDER) !== false) {
            throw new \Exception(self::ARGUMENT_PLACEHOLDER.' is not allowed in test case definition');
        }
        $testcaseName = trim($testcaseName);
        $testcaseName = preg_replace('/\s\s+/i', ' ', $testcaseName);
        preg_match_all('/\s*(\$[a-z][a-z0-9_]*)\s*/i', $testcaseName, $matches);

        $arguments = $matches[1] ?? [];
        foreach ($arguments as &$argument) {
            $argument = new VariableName($argument);
        }

        return $arguments;
    }

    /**
     * Match Testcase's call string and given Testcase name
     * Returns parameters expressions or false if not matched
     *
     * @param string $testcaseCall
     * @param string $testcaseName
     * @return bool|array
     */
    protected function match(string $testcaseCall, string $testcaseName)
    {
        $regexp = str_replace(self::ARGUMENT_PLACEHOLDER, '\s+(\$.+)\s*', preg_quote($testcaseName));
        $regexp = '/^' . str_replace(' ', '\s+', $regexp) . '$/i';
        $flag = preg_match($regexp, $testcaseCall, $matches);

        if (!$flag) {
            return false;
        }
        array_shift($matches);
        return $matches;
    }

}