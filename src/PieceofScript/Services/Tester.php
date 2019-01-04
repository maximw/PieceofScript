<?php

namespace PieceofScript\Services;

use PieceofScript\Services\Contexts\AbstractContext;
use PieceofScript\Services\Errors\ControlFlow\CancelException;
use PieceofScript\Services\Errors\ControlFlow\MustException;
use PieceofScript\Services\Errors\RuntimeError;
use PieceofScript\Services\Out\JunitReport;
use PieceofScript\Services\Out\Out;
use PieceofScript\Services\Values\NullLiteral;
use PieceofScript\Services\Values\NumberLiteral;
use PieceofScript\Services\Contexts\ContextStack;
use PieceofScript\Services\Contexts\EndpointContext;
use PieceofScript\Services\Contexts\GlobalContext;
use PieceofScript\Services\Contexts\TestcaseContext;
use PieceofScript\Services\Endpoints\EndpointsRepository;
use PieceofScript\Services\Errors\FileNotFoundError;
use PieceofScript\Services\Generators\GeneratorsRepository;
use PieceofScript\Services\Parsing\Parser;
use PieceofScript\Services\Parsing\Token;
use PieceofScript\Services\Statistics\Statistics;
use PieceofScript\Services\Testcases\Testcase;
use PieceofScript\Services\Testcases\TestcaseCall;
use PieceofScript\Services\Testcases\TestcasesRepository;
use PieceofScript\Services\Utils\Utils;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\VariableName;

class Tester
{
    const OPERATOR_INCLUDE = 'include';
    const OPERATOR_REQUIRE = 'require';
    const OPERATOR_CONST = 'const';
    const OPERATOR_VAR = 'var';
    const OPERATOR_LET = 'let';
    const OPERATOR_IMPORT = 'import';
    const OPERATOR_ENDPOINT = 'endpoint';
    const OPERATOR_TESTCASE = 'testcase';
    const OPERATOR_ASSERT = 'assert';
    const OPERATOR_MUST = 'must';
    const OPERATOR_RUN = 'run';
    const OPERATOR_PRINT = 'print';
    const OPERATOR_SLEEP ='sleep';
    const OPERATOR_WHILE = 'while';
    const OPERATOR_FOREACH = 'foreach';
    const OPERATOR_IF = 'if';
    const OPERATOR_CANCEL = 'cancel';

    const ALL_OPERATORS = [
        self::OPERATOR_INCLUDE,
        self::OPERATOR_REQUIRE,
        self::OPERATOR_CONST,
        self::OPERATOR_VAR,
        self::OPERATOR_LET,
        self::OPERATOR_IMPORT,
        self::OPERATOR_ENDPOINT,
        self::OPERATOR_TESTCASE,
        self::OPERATOR_ASSERT,
        self::OPERATOR_MUST,
        self::OPERATOR_RUN,
        self::OPERATOR_PRINT,
        self::OPERATOR_SLEEP,
        self::OPERATOR_WHILE,
        self::OPERATOR_FOREACH,
        self::OPERATOR_IF,
        self::OPERATOR_CANCEL,
    ];

    /** @var string Starting file */
    protected $startFile;

    /** @var Parser */
    protected $parser;

    /** @var Statistics */
    protected $statistics;

    /** @var GeneratorsRepository  */
    protected $generators;

    /** @var EndpointsRepository */
    protected $endpoints;

    /** @var TestcasesRepository */
    protected $testcases;

    /** @var FilesRepository */
    protected $files;

    /** @var ContextStack */
    protected $contextStack;

    /** @var JunitReport|null */
    protected $junitReport;

    public function __construct(string $startFile, string $reportFile = null)
    {
        $this->startFile = $startFile;

        $this->contextStack = new ContextStack();
        $this->files = new FilesRepository();

        $this->generators = new GeneratorsRepository();
        $this->endpoints = new EndpointsRepository();
        $this->testcases = new TestcasesRepository();

        $this->parser = new Parser($this->generators, $this->contextStack);
        $this->statistics = new Statistics($this->endpoints->getCount());
        if (null !== $reportFile) {
            $this->junitReport = new JunitReport(
                $reportFile,
                $this->statistics,
                $startFile
            );
        }
    }

    public function run()
    {
        try {
            $context = new GlobalContext('Global', $this->startFile);
            $this->contextStack->push($context);

            $this->executeFile($this->startFile);
            $this->statistics->printStatistics();
            if ($this->junitReport instanceof JunitReport) {
                $this->junitReport->generate();
            }
        } catch (CancelException $e) {
            Out::printCancel();
        } catch (MustException $e) {
            Out::printMustExit($this->contextStack);
        } catch (RuntimeError $e) {
            Out::printError($e, $this->contextStack);
            return 1;
        }

        $this->statistics->printStatistics();
        if ($this->junitReport instanceof JunitReport) {
            $this->junitReport->generate();
        }

        return 0;
    }

    /**
     *  Execute file
     *
     * @param string $fileName
     * @param string|null $relativeDir
     * @throws Errors\ContextStackEmptyException
     * @throws Errors\TestcaseExistsException
     * @throws Errors\TestcaseNotFoundException
     * @throws Errors\FileNotFoundError
     */
    protected function executeFile(string $fileName)
    {
        $lines = $this->files->read($fileName);
        Out::printDebug('Start executing ' . $fileName);
        $this->executeLines($lines, $fileName, 0);
    }

    protected function executeLines(array $lines, string $currentFile, int $offsetLineNumber)
    {
        $this->contextStack->head()
            ->setFile($currentFile)
            ->setLine($offsetLineNumber);

        $totalLines = count($lines);

        for ($lineNumber = 0; $lineNumber < $totalLines; $lineNumber++) {
            $this->contextStack->head()
                ->setFile($currentFile)
                ->setLine($offsetLineNumber + $lineNumber);

            if ($this->isEmptyLine($lines[$lineNumber])) {
                continue;
            }

            list($operator, $expression, $indent) = $this->extractOperator($lines[$lineNumber]);

            Out::printLine($lines[$lineNumber]);

            if (!$this->contextStack->head()->isAllowedOperator($operator)) {
                throw new \Exception('Cannot execute ' . $operator . ' in context');
            }

            if ($operator === self::OPERATOR_TESTCASE) {

                $testcase = $this->testcases->add($expression, $this->contextStack->head()->getFile(), $this->contextStack->head()->getLine());
                $flag = true;
                while ($flag) {
                    $lineNumber = $lineNumber + 1;
                    $flag = false;
                    if ($lineNumber < $totalLines) {
                        $lineIndent = $this->getLineIndent($lines[$lineNumber]);
                        if ($lineIndent > $indent || $this->isEmptyLine($lines[$lineNumber])) {
                            $testcase->addLine($lines[$lineNumber]);
                            $flag = true;
                        } else {
                            $lineNumber = $lineNumber - 1;
                        }
                    }
                }

            } elseif ($operator === self::OPERATOR_WHILE) {

                $blockBody = $this->getBlockBody($lines, $totalLines, $lineNumber, $indent);

                $flag = $this->parser->evaluate($expression, $this->contextStack)->toBool()->getValue();
                while ($flag) {
                    $this->executeLines($blockBody, $currentFile, $lineNumber + 1 + $offsetLineNumber);
                    $flag = $this->parser->evaluate($expression, $this->contextStack)->toBool()->getValue();
                }
                $lineNumber = $lineNumber + count($blockBody);

            } elseif ($operator === self::OPERATOR_FOREACH) {

                $blockBody = $this->getBlockBody($lines, $totalLines, $lineNumber, $indent);

                $splitTokens = $this->parser->tokenizeSplitBy($expression, Token::T_SEMICOLON);
                if (count($splitTokens) !== 2 && count($splitTokens) !== 3) {
                    throw new \Exception('Error parsing foreach');
                }
                $array = $this->parser->evaluate($splitTokens[0], $this->contextStack);
                if (!$array instanceof ArrayLiteral) {
                    throw new \Exception('Cannot iterate over ' . $array::TYPE_NAME);
                }
                $withKey = count($splitTokens) === 3;

                $valueName = $this->parser->extractOperand($withKey ? $splitTokens[2] : $splitTokens[1], $this->contextStack);
                if (!$valueName instanceof VariableName) {
                    throw new \Exception('Error parsing foreach');
                }
                if (!$valueName->isSimple() || !$valueName->mode === VariableName::MODE_VALUE) {
                    throw new \Exception('Error parsing foreach');
                }
                if ($withKey) {
                    $keyName = $this->parser->extractOperand($splitTokens[1], $this->contextStack);
                    if (!$keyName instanceof VariableName) {
                        throw new \Exception('Error parsing foreach');
                    }
                    if (!$keyName->isSimple() || !$keyName->mode === VariableName::MODE_VALUE) {
                        throw new \Exception('Error parsing foreach');
                    }
                }

                foreach ($array as $key => $value) {
                    $this->contextStack->head()->setVariable($valueName, $value, AbstractContext::ASSIGNMENT_MODE_VARIABLE);
                    if ($withKey) {
                        $this->contextStack->head()->setVariable($keyName, Utils::wrapValueContainer($key), AbstractContext::ASSIGNMENT_MODE_VARIABLE);
                    }
                    $this->executeLines($blockBody, $currentFile, $lineNumber + 1 + $offsetLineNumber);
                }
                $lineNumber = $lineNumber + count($blockBody);

            } elseif ($operator === self::OPERATOR_IF) {

                $blockBody = $this->getBlockBody($lines, $totalLines, $lineNumber, $indent);

                $flag = $this->parser->evaluate($expression, $this->contextStack)->toBool()->getValue();
                if ($flag) {
                    $this->executeLines($blockBody, $currentFile, $lineNumber + 1 + $offsetLineNumber);
                }
                $lineNumber = $lineNumber + count($blockBody);

            } else {

                $this->executeOperator($operator, $expression, $indent, $currentFile, $lineNumber + $offsetLineNumber);

            }
        }
    }


    /**
     * Execute one-line operator
     *
     * @param string $operator
     * @param string $expression
     * @param int $indent
     * @param $fileName
     * @param int $lineNumber
     * @throws Errors\ContextStackEmptyException
     * @throws Errors\FileNotFoundError
     * @throws Errors\TestcaseExistsException
     * @throws Errors\TestcaseNotFoundException
     */
    protected function executeOperator(string $operator, string $expression, int $indent, string $fileName, int $lineNumber)
    {
        if ($operator === self::OPERATOR_REQUIRE) {

            $requiredFile = $this->parser->evaluate($expression, $this->contextStack)->getValue();
            $this->executeFile($requiredFile);
            $this->contextStack->head()
                ->setFile($fileName)
                ->setLine($lineNumber);

        } elseif ($operator === self::OPERATOR_INCLUDE) {

            $filesMask = $this->parser->evaluate($expression, $this->contextStack)->getValue();
            $files = Utils::fileSearch($filesMask, true);
            foreach ($files as $file) {
                try {
                    $this->executeFile($file);
                } catch (FileNotFoundError $e) {
                    Out::printWarning($e->getMessage(), $this->contextStack);
                }
            }
            $this->contextStack->head()
                ->setFile($fileName)
                ->setLine($lineNumber);

        } elseif ($operator === self::OPERATOR_CONST) {

            $this->operatorConst($expression);

        } elseif ($operator === self::OPERATOR_VAR) {

            $this->operatorVar($expression);

        } elseif ($operator === self::OPERATOR_LET) {

            $this->operatorLet($expression);

        } elseif ($operator === self::OPERATOR_IMPORT) {

            $this->operatorImport($expression);

        } elseif ($operator === self::OPERATOR_ENDPOINT) {

            $this->callEndpoint($expression);

        } elseif ($operator === self::OPERATOR_ASSERT) {

            $this->operatorAssert($expression);

        } elseif ($operator === self::OPERATOR_MUST) {

            $this->operatorMust($expression);


        } elseif ($operator === self::OPERATOR_RUN) {

            $this->operatorRun($expression);

        } elseif ($operator === self::OPERATOR_PRINT) {

            $this->operatorPrint($expression);

        } elseif ($operator === self::OPERATOR_SLEEP) {

            $this->operatorSleep($expression);

        } elseif ($operator === self::OPERATOR_CANCEL) {

            throw new CancelException();

        }
    }

    /**
     * Call API endpoint
     * @param $line
     */
    protected function callEndpoint($line)
    {
        $endpointCall = $this->endpoints->getByCall($line);

        $parametersCount = count($endpointCall->getParameters());
        $argumentsCount = count($endpointCall->getEndpoint()->getArguments());

        if ($argumentsCount > $parametersCount) {
            throw new \Exception('Not enough parameters given to ' . $endpointCall->getEndpoint()->getName());
        }
        if ($argumentsCount < $parametersCount) {
            Out::printWarning('Too many parameters given to ' . $endpointCall->getEndpoint()->getName(), $this->contextStack);
        }

        // Get all references
        $references = [];
        foreach ($endpointCall->getParameters() as $parameter) {
            $variable = $this->parser->extractOperand($parameter, $this->contextStack);
            if ($variable instanceof VariableName) {
                $references[] = $this->contextStack->head()->getReference($variable);
            }
        }
        if ($argumentsCount > count($references)) {
            throw new \Exception('Not enough parameters given to ' . $endpointCall->getEndpoint()->getOriginalName());
        }

        // Push Endpoint Context
        $context = new EndpointContext(
            $endpointCall->getEndpoint()->getOriginalName(),
            $endpointCall->getEndpoint()->getFile()
        );
        $this->contextStack->push($context);

        // Set all references
        for ($i = 0; $i < $argumentsCount; $i++) {
            $context->setReference($endpointCall->getEndpoint()->getArguments()[$i], $references[$i]);
        }

        // Init $request and $response variables
        $requestVarName = new VariableName('$request');
        $responseVarName = new VariableName('$response');
        $request = Utils::wrapValueContainer([
            'method' => $this->parser->evaluate($endpointCall->getEndpoint()->getHttpMethod(), $this->contextStack),
            'url' => $this->parser->evaluate($endpointCall->getEndpoint()->getUrl(), $this->contextStack),
            'headers' => $this->parser->evaluate($endpointCall->getEndpoint()->getHeaders(), $this->contextStack),
            'cookies' => $this->parser->evaluate($endpointCall->getEndpoint()->getCookies(), $this->contextStack),
            'auth' => $this->parser->evaluate($endpointCall->getEndpoint()->getAuth(), $this->contextStack),
            'query' => $this->parser->evaluate($endpointCall->getEndpoint()->getQuery(), $this->contextStack),
            'format' => strtolower($this->parser->evaluate($endpointCall->getEndpoint()->getFormat(), $this->contextStack)->getValue()),
            'data' => $this->parser->evaluate($endpointCall->getEndpoint()->getData(), $this->contextStack),
            ]);
        $this->contextStack->head()->setVariable($requestVarName, $request, AbstractContext::ASSIGNMENT_MODE_VARIABLE);
        $this->contextStack->head()->setVariable($responseVarName, new NullLiteral(), AbstractContext::ASSIGNMENT_MODE_VARIABLE);
        $this->contextStack->neck()->setVariable($requestVarName, $request, AbstractContext::ASSIGNMENT_MODE_VARIABLE);
        $this->contextStack->neck()->setVariable($responseVarName, new NullLiteral(), AbstractContext::ASSIGNMENT_MODE_VARIABLE);

        $this->statistics->addCall($endpointCall, $this->contextStack, $request, new ArrayLiteral());

        // Execute "before" section
        $this->executeLines($endpointCall->getEndpoint()->getBefore(), $endpointCall->getEndpoint()->getFile(), 0);

        $request = $this->contextStack->head()->getVariable($requestVarName);

        $this->statistics->setRequest($request);
        Out::printRequest($request);
        $response = HttpClient::doRequest($request, $this->contextStack, $endpointCall);
        $this->contextStack->head()->setVariable($responseVarName, $response, AbstractContext::ASSIGNMENT_MODE_VARIABLE);
        $this->contextStack->neck()->setVariable($responseVarName, $response, AbstractContext::ASSIGNMENT_MODE_VARIABLE);
        $this->statistics->setResponse($response);
        Out::printResponse($response);

        // Execute "after" section
        $this->executeLines($endpointCall->getEndpoint()->getAfter(), $endpointCall->getEndpoint()->getFile(), 0);

        $this->contextStack->pop();
    }

    protected function operatorAssert(string $expression)
    {
        $success = $this->parser->evaluate($expression, $this->contextStack)->toBool()->getValue();
        $this->statistics->addAssertion($expression, $success, $this->contextStack);
        Out::printAssert($expression, $success);
        return $success;
    }

    protected function operatorMust(string $expression)
    {
        $success = $this->operatorAssert($expression);
        if (!$success) {
            throw new MustException();
        }
    }

    /**
     * Create constant value in current context
     *
     * @param string $expression
     * @throws Errors\ContextStackEmptyException
     */
    protected function operatorConst(string $expression)
    {
        $splitTokens = $this->parser->tokenizeSplitBy($expression, Token::T_SEMICOLON);

        $this->contextStack->head()->isGlobalWritable = false;
        $this->contextStack->head()->assignmentMode = AbstractContext::ASSIGNMENT_MODE_CONST;
        foreach ($splitTokens as $tokens) {
            $this->parser->evaluate($tokens, $this->contextStack);
        }
        $this->contextStack->head()->isGlobalWritable = true;
        $this->contextStack->head()->assignmentMode = AbstractContext::ASSIGNMENT_MODE_OFF;
    }
    /**
     * Create and/or set variables values in current context
     *
     * @param string $expression
     * @throws Errors\ContextStackEmptyException
     */
    protected function operatorVar(string $expression)
    {
        $splitTokens = $this->parser->tokenizeSplitBy($expression, Token::T_SEMICOLON);

        $this->contextStack->head()->isGlobalWritable = false;
        $this->contextStack->head()->assignmentMode = AbstractContext::ASSIGNMENT_MODE_VARIABLE;
        foreach ($splitTokens as $tokens) {
            $this->parser->evaluate($tokens, $this->contextStack);
        }
        $this->contextStack->head()->isGlobalWritable = true;
        $this->contextStack->head()->assignmentMode = AbstractContext::ASSIGNMENT_MODE_OFF;
    }

    /**
     * Create and/or set variables values if it exists in current context, otherwise use global context
     *
     * @param string $expression
     * @throws Errors\ContextStackEmptyException
     */
    protected function operatorLet(string $expression)
    {
        $splitTokens = $this->parser->tokenizeSplitBy($expression, Token::T_SEMICOLON);

        $this->contextStack->head()->assignmentMode = AbstractContext::ASSIGNMENT_MODE_VARIABLE;
        foreach ($splitTokens as $tokens) {
            $this->parser->evaluate($tokens, $this->contextStack);
        }
        $this->contextStack->head()->assignmentMode = AbstractContext::ASSIGNMENT_MODE_OFF;
    }

    /**
     * Copy variables from global to current context
     *
     * @param string $expression
     * @throws Errors\ContextStackEmptyException
     */
    protected function operatorImport(string $expression)
    {
        if ($this->contextStack->head() instanceof GlobalContext) {
            throw new \Exception('Cannot import to global context');
        }

        $expression = trim($expression);
        $splitTokens = $this->parser->tokenizeSplitBy($expression, Token::T_SEMICOLON);

        foreach ($splitTokens as $tokens) {
            $variableName = $this->parser->extractOperand($tokens, $this->contextStack);
            if (!$variableName instanceof VariableName) {
                throw new \Exception('Error parsing import variable');
            }
            if (!$variableName->isSimple()) {
                throw new \Exception('Cannot import array element, only whole array');
            }

            if ($this->contextStack->global()->hasVariable($variableName)) {
                $value = $this->contextStack->global()->getVariable($variableName);
                $this->contextStack->head()->setVariable($variableName, $value, AbstractContext::ASSIGNMENT_MODE_VARIABLE);
            } else {
                throw new \Exception('Cannot import variable ' . (string) $variableName . ', it does not exist');
            }
        }
    }

    /**
     * Run Testcases
     *
     * @param string $expression
     * @throws \Exception
     */
    protected function operatorRun(string $expression)
    {
        $expression = trim($expression);
        if ($expression !== '') {
            $testcaseCall = $this->testcases->getByCall($expression);
            $this->runTestcase($testcaseCall);
        } else {
            $testcases = $this->testcases->getWithoutArguments();
            /** @var Testcase $testcase */
            foreach ($testcases as $testcase) {
                if (!$testcase->hasArguments()) {
                    $testcaseCall = new TestcaseCall($testcase);
                    try {
                        $this->runTestcase($testcaseCall);
                    } catch (MustException $exception) {

                    }
                }
            }
        }
    }

    /**
     * Run one Testcase
     *
     * @param TestcaseCall $testcaseCall
     * @throws Errors\ContextStackEmptyException
     */
    protected function runTestcase(TestcaseCall $testcaseCall)
    {
        $parametersCount = count($testcaseCall->parameters);
        $argumentsCount = count($testcaseCall->testcase->arguments);

        if ($argumentsCount > $parametersCount) {
            throw new \Exception('Not enough parameters given to ' . $testcaseCall->testcase->name);
        }
        if ($argumentsCount < $parametersCount) {
            Out::printWarning('Too many parameters given to ' . $testcaseCall->testcase->name, $this->contextStack);
        }

        $this->statistics->endCurrentCall();

        // Get all references
        $references = [];
        foreach ($testcaseCall->parameters as $parameter) {
            $variable = $this->parser->extractOperand($parameter, $this->contextStack);
            if ($variable instanceof VariableName) {
                $references[] = $this->contextStack->head()->getReference($variable);
            }
        }
        if ($argumentsCount > count($references)) {
            throw new \Exception('Not enough parameters given to ' . $testcaseCall->testcase->name);
        }

        // Push Testcase Context
        $context = new TestcaseContext(
            $testcaseCall->testcase->originalName,
            $testcaseCall->testcase->file,
            $testcaseCall->testcase->lineNumber
        );
        $this->contextStack->push($context);
        $requestVarName = new VariableName('$request');
        $responseVarName = new VariableName('$response');
        $this->contextStack->head()->setVariable($requestVarName, new NullLiteral(), AbstractContext::ASSIGNMENT_MODE_VARIABLE);
        $this->contextStack->head()->setVariable($responseVarName, new NullLiteral(), AbstractContext::ASSIGNMENT_MODE_VARIABLE);

        // Set all references
        for ($i = 0; $i < $argumentsCount; $i++) {
            $context->setReference($testcaseCall->testcase->arguments[$i], $references[$i]);
        }

        $this->executeLines($testcaseCall->testcase->lines, $testcaseCall->testcase->file, $testcaseCall->testcase->lineNumber + 1);

        $this->contextStack->pop();
    }

    /**
     * Print values to stdout
     *
     * @param string $expression
     * @throws \Exception
     */
    protected function operatorPrint(string $expression)
    {
        $splitTokens = $this->parser->tokenizeSplitBy($expression, Token::T_SEMICOLON);
        $printable = [];
        foreach ($splitTokens as $tokens) {
            $value = $this->parser->evaluate($tokens, $this->contextStack);
            $printable[] = $value->toPrint();
        }
        Out::printValues($printable);
    }

    /**
     * @param string $expression
     * @throws RuntimeError
     */
    protected function operatorSleep(string $expression)
    {
        $value = $this->parser->evaluate($expression, $this->contextStack);
        if (!$value instanceof NumberLiteral) {
            throw new RuntimeError('Sleep required Number of microseconds. ' . $value::TYPE_NAME . ' given');
        }
        usleep((int) $value->getValue());
    }

    /**
     * Check and extract operator from line
     * If operator not found attempt to call API endpoint
     *
     * @param string $line
     * @return array
     */
    protected function extractOperator(string $line)
    {
        $indent = $this->getLineIndent($line);
        $line = trim($line);

        if ($line[0] == '>') {
            return [self::OPERATOR_ENDPOINT, trim(substr($line, 1)), $indent];
        }

        foreach (static::ALL_OPERATORS as $operator) {
            if (strripos($line, $operator . ' ') === 0 || strtolower($line) === strtolower($operator)) {
                $expression = trim(substr($line, strlen($operator) + 1));
                return [$operator, $expression, $indent];
            }
        }
        return [self::OPERATOR_ENDPOINT, $line, $indent];
    }

    /**
     * Get lines with indent more than given
     *
     * @param array $lines
     * @param int $totalLines
     * @param int $lineNumber
     * @param int $indent
     * @return array
     */
    protected function getBlockBody(array $lines, int $totalLines, int $lineNumber, int $indent): array
    {
        $flag = true;
        $blockBody = [];
        while ($flag) {
            $lineNumber = $lineNumber + 1;
            $flag = false;
            if ($lineNumber < $totalLines) {
                $lineIndent = $this->getLineIndent($lines[$lineNumber]);
                if ($lineIndent > $indent || $this->isEmptyLine($lines[$lineNumber])) {
                    $blockBody[] = $lines[$lineNumber];
                    $flag = true;
                } else {
                    $lineNumber = $lineNumber - 1;
                }
            }
        }
        return $blockBody;
    }

    /**
     * Get indent length of line
     *
     * @param string $line
     * @return int
     */
    protected function getLineIndent(string $line): int
    {
        return strlen($line) - strlen(ltrim($line));
    }

    /**
     * Check if line is empty or just comment
     *
     * @param string $line
     * @return bool
     */
    protected function isEmptyLine(string $line): bool
    {
        $line = trim($line);
        return $line === '' || strpos($line, '//') === 0;
    }


}