<?php

namespace PieceofScript\Services;

use PieceofScript\Services\Call\BaseCall;
use PieceofScript\Services\Contexts\AbstractContext;
use PieceofScript\Services\Contexts\OptionsContext;
use PieceofScript\Services\Endpoints\Endpoint;
use PieceofScript\Services\Errors\ControlFlow\CancelException;
use PieceofScript\Services\Errors\ControlFlow\MustException;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Errors\Parser\EmptyExpressionError;
use PieceofScript\Services\Errors\Parser\VariableError;
use PieceofScript\Services\Errors\RuntimeError;
use PieceofScript\Services\Errors\TestcaseNotFoundException;
use PieceofScript\Services\Out\HtmlReport;
use PieceofScript\Services\Out\In;
use PieceofScript\Services\Out\JunitReport;
use PieceofScript\Services\Out\Out;
use PieceofScript\Services\Parsing\CallLexer;
use PieceofScript\Services\Parsing\ExpressionLexer;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NullLiteral;
use PieceofScript\Services\Values\NumberLiteral;
use PieceofScript\Services\Contexts\ContextStack;
use PieceofScript\Services\Contexts\EndpointContext;
use PieceofScript\Services\Contexts\GlobalContext;
use PieceofScript\Services\Contexts\TestcaseContext;
use PieceofScript\Services\Endpoints\EndpointsRepository;
use PieceofScript\Services\Errors\FileNotFoundError;
use PieceofScript\Services\Generators\GeneratorsRepository;
use PieceofScript\Services\Parsing\Evaluator;
use PieceofScript\Services\Parsing\Token;
use PieceofScript\Services\Statistics\Statistics;
use PieceofScript\Services\Testcases\Testcase;
use PieceofScript\Services\Testcases\TestcaseCall;
use PieceofScript\Services\Testcases\TestcasesRepository;
use PieceofScript\Services\Utils\Utils;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\VariableName;
use PieceofScript\Services\Values\VariableReference;

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
    const OPERATOR_PAUSE = 'pause';
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
        self::OPERATOR_PAUSE,
        self::OPERATOR_WHILE,
        self::OPERATOR_FOREACH,
        self::OPERATOR_IF,
        self::OPERATOR_CANCEL,
    ];

    /** @var string Starting file */
    protected $startFile;

    /** @var Evaluator */
    protected $evaluator;

    /** @var CallLexer */
    protected $callLexer;

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

    /** @var HtmlReport|null */
    protected $htmlReport;

    /**
     * Tester constructor.
     * @param string $startFile
     * @param string|null $junitFile
     * @param string|null $htmlFile
     */
    public function __construct(
        string $startFile,
        string $junitFile = null,
        string $htmlFile = null
    )
    {
        $this->startFile = $startFile;

        $this->contextStack = new ContextStack();
        $this->files = new FilesRepository();
        $this->callLexer = new CallLexer();

        $this->generators = new GeneratorsRepository();
        $this->endpoints = new EndpointsRepository($this->callLexer);
        $this->testcases = new TestcasesRepository($this->callLexer);

        $this->evaluator = new Evaluator($this->generators, $this->contextStack, new ExpressionLexer());
        $this->statistics = new Statistics($this->endpoints);
        if (null !== $junitFile) {
            $this->junitReport = new JunitReport(
                $junitFile,
                $this->statistics,
                $startFile
            );
        }
        if (null !== $htmlFile) {
            $this->htmlReport = new HtmlReport(
                $htmlFile,
                $this->statistics,
                $startFile
            );
        }
    }

    public function run()
    {
        $executionResult = 0;
        try {
            $context = new GlobalContext('Global', $this->startFile);
            $this->contextStack->push($context);

            $this->executeFile($this->startFile);
        } catch (CancelException $e) {
            Out::printCancel();
        } catch (MustException $e) {
            Out::printMustExit($this->contextStack);
        } catch (RuntimeError $e) {
            Out::printError($e, $this->contextStack);
            $executionResult = 1;
        } catch (\Exception $e) {
            Out::printDebug($e->getMessage());
            Out::printContextStack($this->contextStack);
            $executionResult = 1;
        }

        $this->statistics->prepareStatistics();
        $this->statistics->printStatistics();
        if ($this->junitReport instanceof JunitReport) {
            $this->junitReport->generate();
        }
        if ($this->htmlReport instanceof HtmlReport) {
            $this->htmlReport->generate();
        }

        return $executionResult;
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
        Out::printDebug('End executing ' . $fileName);
    }

    /**
     * Execute all given lines
     *
     * @param array $lines
     * @param string $currentFile
     * @param int $offsetLineNumber
     * @throws CancelException
     * @throws Errors\ContextStackEmptyException
     * @throws Errors\TestcaseExistsException
     * @throws FileNotFoundError
     * @throws MustException
     * @throws RuntimeError
     * @throws TestcaseNotFoundException
     * @throws VariableError
     */
    protected function executeLines(array $lines, string $currentFile, int $offsetLineNumber)
    {
        $totalLines = count($lines);

        for ($lineNumber = 0; $lineNumber < $totalLines; $lineNumber++) {

            $this->contextStack->head()
                ->setFile($currentFile)
                ->setLine($offsetLineNumber + $lineNumber);

            $currentCommandLine = $lineNumber;
            $line = $this->getLine($lines, $lineNumber);

            if ($this->isEmptyLine($line)) {
                continue;
            }

            list($operator, $expression, $indent) = $this->extractOperator($line);

            Out::printLine($line, $currentCommandLine + $offsetLineNumber);

            if (!$this->contextStack->head()->isAllowedOperator($operator)) {
                throw new RuntimeError('Cannot execute ' . $operator . ' in context');
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

                $flag = $this->evaluator->evaluate($expression, $this->contextStack->head())->toBool()->getValue();
                while ($flag) {
                    $this->executeLines($blockBody, $currentFile, $lineNumber + 1 + $offsetLineNumber);
                    $flag = $this->evaluator->evaluate($expression, $this->contextStack->head())->toBool()->getValue();
                }
                $lineNumber = $lineNumber + count($blockBody);

            } elseif ($operator === self::OPERATOR_FOREACH) {

                $blockBody = $this->getBlockBody($lines, $totalLines, $lineNumber, $indent);

                $splitTokens = $this->evaluator->tokenizeSplitBy($expression, Token::T_SEMICOLON);
                if (count($splitTokens) !== 2 && count($splitTokens) !== 3) {
                    throw new \Exception('Error parsing foreach', $this->contextStack);
                }
                $array = $this->evaluator->evaluate($splitTokens[0], $this->contextStack->head());
                if (!$array instanceof ArrayLiteral) {
                    throw new \Exception('Cannot iterate over ' . $array::TYPE_NAME);
                }
                $withKey = count($splitTokens) === 3;

                $valueName = $this->evaluator->extractOperand($withKey ? $splitTokens[2] : $splitTokens[1], $this->contextStack->head());
                if (!$valueName instanceof VariableName) {
                    throw new \Exception('Error parsing foreach');
                }
                if (!$valueName->isSimple() || !$valueName->mode === VariableName::MODE_VALUE) {
                    throw new \Exception('Error parsing foreach');
                }
                if ($withKey) {
                    $keyName = $this->evaluator->extractOperand($splitTokens[1], $this->contextStack->head());
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

                $flag = $this->evaluator->evaluate($expression, $this->contextStack->head())->toBool()->getValue();
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
     * Get line from lines array, considering line breaks
     *
     * @param array $lines
     * @param int $lineNumber
     * @return string
     */
    protected function getLine(array $lines, int &$lineNumber): string
    {
        $totalLines = count($lines);
        $line = rtrim($lines[$lineNumber], "\r\n");
        while (substr(trim($line), -1, 1) == '\\' && $lineNumber < $totalLines) {
            $lineNumber = $lineNumber + 1;
            $line = rtrim(rtrim($line), '\\') . $lines[$lineNumber];
        }
        return $line;
    }

    /**
     * Execute one operator
     *
     * @param string $operator
     * @param string $expression
     * @param int $indent
     * @param string $fileName
     * @param int $lineNumber
     * @throws CancelException
     * @throws Errors\ContextStackEmptyException
     * @throws Errors\TestcaseExistsException
     * @throws FileNotFoundError
     * @throws MustException
     * @throws RuntimeError
     * @throws TestcaseNotFoundException
     */
    protected function executeOperator(string $operator, string $expression, int $indent, string $fileName, int $lineNumber)
    {
        if ($operator === self::OPERATOR_REQUIRE) {

            $requiredFile = $this->evaluator->evaluate($expression, $this->contextStack->head())->getValue();
            $this->executeFile($requiredFile);
            Out::printDebug('Continue executing ' . $fileName);
            $this->contextStack->head()
                ->setFile($fileName)
                ->setLine($lineNumber);

        } elseif ($operator === self::OPERATOR_INCLUDE) {

            $filesMask = $this->evaluator->evaluate($expression, $this->contextStack->head())->getValue();
            $files = Utils::fileSearch($filesMask, true);
            foreach ($files as $file) {
                try {
                    $this->executeFile($file);
                    Out::printDebug('Continue executing ' . $fileName);
                } catch (FileNotFoundError $e) {
                    Out::printWarning($e->getMessage(), $this->contextStack->head());
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

            try {
                $this->callEndpoint($expression);
            } catch (MustException $exception) {
                Out::printMustExit($this->contextStack);
            }

        } elseif ($operator === self::OPERATOR_ASSERT) {

            $this->operatorAssert($expression);

        } elseif ($operator === self::OPERATOR_MUST) {

            $this->operatorMust($expression);

        } elseif ($operator === self::OPERATOR_RUN) {

            try {
                $this->operatorRun($expression);
            } catch (MustException $exception) {
                Out::printMustExit($this->contextStack);
            }

        } elseif ($operator === self::OPERATOR_PRINT) {

            $this->operatorPrint($expression);

        } elseif ($operator === self::OPERATOR_SLEEP) {

            $this->operatorSleep($expression);

        } elseif ($operator === self::OPERATOR_PAUSE) {

            $this->operatorPause($expression);

        } elseif ($operator === self::OPERATOR_CANCEL) {

            throw new CancelException();

        }
    }

    /**
     * Call API endpoint
     * @param $line
     * @throws Errors\ContextStackEmptyException
     * @throws Errors\InternalError
     * @throws RuntimeError
     * @throws VariableError
     */
    protected function callEndpoint($line)
    {
        $call = $this->callLexer->getCall($line);
        $endpoint = $this->endpoints->getByCall($call);
        if (!$endpoint instanceof Endpoint) {
            throw new RuntimeError('Endpoint not found: ' . $line);
        }

        // Evaluate passed options
        $optionsContext = new OptionsContext(
            $this->contextStack->head()->getName(),
            $this->contextStack->head()->getFile(),
            $this->contextStack->head()->getLine()
        );
        $optionsContext->isGlobalReadable = true;
        $optionsContext->isGlobalWritable = false;
        $this->contextStack->push($optionsContext);
        $options = $call->getOptions();
        foreach ($options as $option) {
            $splitTokens = $this->evaluator->queueSplitBy($option->getValue(), Token::T_SEMICOLON);
            foreach ($splitTokens as $tokens) {
                $this->evaluator->evaluate($tokens, $optionsContext);
            }
        }
        $this->contextStack->pop();

        // Endpoint Context
        $context = new EndpointContext(
            $endpoint->getDefinition()->getOriginalString(),
            $endpoint->getFile()
        );
        $this->contextStack->push($context);

        // Evaluate default options
        $context->assignmentMode = AbstractContext::ASSIGNMENT_MODE_VARIABLE;
        $context->isGlobalReadable = true;
        $context->isGlobalWritable = false;
        $options = $endpoint->getDefinition()->getOptions();
        foreach ($options as $option) {
            $splitTokens = $this->evaluator->queueSplitBy($option->getValue(), Token::T_SEMICOLON);
            foreach ($splitTokens as $tokens) {
                $this->evaluator->evaluate($tokens, $context);
            }
        }
        // Prepare and push Endpoint Context
        $context->importVariableValues($optionsContext);
        $context->isGlobalReadable = true;

        // Set all parameters
        $arguments = $endpoint->getDefinition()->getArguments();
        $parameters = $call->getArguments();
        $argumentsCount = count($arguments);
        if ($argumentsCount !== count($parameters)) {
            throw new RuntimeError('Not enough parameters given to ' . $endpoint->getDefinition()->getOriginalString());
        }
        for ($i = 0; $i < $argumentsCount; $i++) {
            $argumentVar = $this->evaluator->extractOperand($arguments[$i]->getValue(), $context);
            if ($parameters[$i]->isByReference()) {
                $parameterVar = $this->evaluator->extractOperand($parameters[$i]->getValue(), $context);
                $reference = $this->contextStack->neck()->getReference($parameterVar);
                $context->setReference($argumentVar, $reference);
            } else {
                $parameterVal = $this->evaluator->evaluate($parameters[$i]->getValue(), $context);
                $context->setVariable($argumentVar, $parameterVal, AbstractContext::ASSIGNMENT_MODE_VARIABLE);
            }
        }

        // Init $request and $response variables
        $requestVarName = new VariableName('$request');
        $responseVarName = new VariableName('$response');
        $request = Utils::wrapValueContainer([
            'method' => $this->evaluator->evaluate($endpoint->getHttpMethod(), $this->contextStack->head()),
            'url' => $this->evaluator->evaluate($endpoint->getUrl(), $this->contextStack->head()),
            'headers' => $this->evaluator->evaluate($endpoint->getHeaders(), $this->contextStack->head()),
            'cookies' => $this->evaluator->evaluate($endpoint->getCookies(), $this->contextStack->head()),
            'auth' => $this->evaluator->evaluate($endpoint->getAuth(), $this->contextStack->head()),
            'query' => $this->evaluator->evaluate($endpoint->getQuery(), $this->contextStack->head()),
            'format' => strtolower($this->evaluator->evaluate($endpoint->getFormat(), $this->contextStack->head())->getValue()),
            'data' => $this->evaluator->evaluate($endpoint->getData(), $this->contextStack->head()),
            ]);
        $this->contextStack->head()->setVariable($requestVarName, $request, AbstractContext::ASSIGNMENT_MODE_VARIABLE);
        $this->contextStack->head()->setVariable($responseVarName, new NullLiteral(), AbstractContext::ASSIGNMENT_MODE_VARIABLE);
        $this->contextStack->neck()->setVariable($requestVarName, $request, AbstractContext::ASSIGNMENT_MODE_VARIABLE);
        $this->contextStack->neck()->setVariable($responseVarName, new NullLiteral(), AbstractContext::ASSIGNMENT_MODE_VARIABLE);

        $this->statistics->addCall($line, $endpoint, $this->contextStack, $request, new ArrayLiteral());

        // Execute "before" section
        $this->executeLines($endpoint->getBefore(), $endpoint->getFile(), 0);

        $request = $this->contextStack->head()->getVariable($requestVarName);

        $this->statistics->setRequest($request);
        Out::printRequest($request);
        $response = HttpClient::doRequest($request, $this->contextStack, $endpoint);
        $this->contextStack->head()->setVariable($responseVarName, $response, AbstractContext::ASSIGNMENT_MODE_VARIABLE);
        $this->contextStack->neck()->setVariable($responseVarName, $response, AbstractContext::ASSIGNMENT_MODE_VARIABLE);
        $this->statistics->setResponse($response);
        Out::printResponse($response);

        // Execute "after" section
        $this->executeLines($endpoint->getAfter(), $endpoint->getFile(), 0);

        $this->contextStack->pop();
    }

    /**
     * @param string $expression
     * @return bool
     * @throws Errors\ContextStackEmptyException
     */
    protected function operatorAssert(string $expression)
    {
        $message = '';
        $success = null;
        try {
            $success = $this->evaluator->evaluate($expression, $this->contextStack->head())->toBool()->getValue();
        } catch (VariableError $e) {
            $message = $e->getMessage();
        }
        $usedVariables = $this->evaluator->getUsedVariables($expression);
        $this->statistics->addAssertion($expression, $success, $this->contextStack, $usedVariables, $message);
        Out::printAssert($expression, $success, $message);
        return $success;
    }

    /**
     * @param string $expression
     * @throws Errors\ContextStackEmptyException
     * @throws MustException
     */
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
        $splitTokens = $this->evaluator->tokenizeSplitBy($expression, Token::T_SEMICOLON);

        $this->contextStack->head()->isGlobalWritable = false;
        $this->contextStack->head()->assignmentMode = AbstractContext::ASSIGNMENT_MODE_CONST;
        foreach ($splitTokens as $tokens) {
            $this->evaluator->evaluate($tokens, $this->contextStack->head());
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
        $splitTokens = $this->evaluator->tokenizeSplitBy($expression, Token::T_SEMICOLON);

        $this->contextStack->head()->isGlobalWritable = false;
        $this->contextStack->head()->assignmentMode = AbstractContext::ASSIGNMENT_MODE_VARIABLE;
        foreach ($splitTokens as $tokens) {
            $this->evaluator->evaluate($tokens, $this->contextStack->head());
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
        $splitTokens = $this->evaluator->tokenizeSplitBy($expression, Token::T_SEMICOLON);

        $this->contextStack->head()->assignmentMode = AbstractContext::ASSIGNMENT_MODE_VARIABLE;
        foreach ($splitTokens as $tokens) {
            $this->evaluator->evaluate($tokens, $this->contextStack->head());
        }
        $this->contextStack->head()->assignmentMode = AbstractContext::ASSIGNMENT_MODE_OFF;
    }

    /**
     * Copy variables from global to current context
     *
     * @param string $expression
     * @throws Errors\ContextStackEmptyException
     * @throws VariableError
     */
    protected function operatorImport(string $expression)
    {
        if ($this->contextStack->head() instanceof GlobalContext) {
            throw new \Exception('Cannot import to global context');
        }

        $expression = trim($expression);
        $splitTokens = $this->evaluator->tokenizeSplitBy($expression, Token::T_SEMICOLON);

        foreach ($splitTokens as $tokens) {
            $variableName = $this->evaluator->extractOperand($tokens, $this->contextStack->head());
            if (!$variableName instanceof VariableName) {
                throw new \Exception('Error parsing import variable');
            }
            if (!$variableName->isSimple()) {
                throw new \Exception('Cannot import array element, only whole array');
            }

            if ($this->contextStack->global()->hasVariable($variableName)) {
                $value = $this->contextStack->global()->getVariable($variableName);
                $this->contextStack->head()->isGlobalWritable = false;
                $this->contextStack->head()->setVariable($variableName, $value, AbstractContext::ASSIGNMENT_MODE_VARIABLE);
                $this->contextStack->head()->isGlobalWritable = true;
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
        $call = $this->callLexer->getCall($expression);
        if (count($call->getItems()) === 0) {
            $testcases = $this->testcases->getWithoutArguments();
            /** @var Testcase $testcase */
            foreach ($testcases as $testcase) {
                try {
                    $this->runTestcase($testcase, $call);
                } catch (MustException $exception) {
                    Out::printMustExit($this->contextStack);
                }
            }
        } else {
            $testcase = $this->testcases->getByCall($call);
            if ($testcase instanceof Testcase) {
                $this->runTestcase($testcase, $call);
            } else {
                throw new TestcaseNotFoundException($call->getOriginalString());
            }
        }
    }

    /**
     * Run one Testcase
     *
     * @param Testcase $testcase
     * @param BaseCall $call
     * @throws Errors\ContextStackEmptyException
     * @throws RuntimeError
     * @throws VariableError
     */
    protected function runTestcase(Testcase $testcase, BaseCall $call)
    {
        $this->statistics->endCurrentCall();

        // Evaluate passed options
        $optionsContext = new OptionsContext(
            $this->contextStack->head()->getName(),
            $this->contextStack->head()->getFile(),
            $this->contextStack->head()->getLine()
        );
        $optionsContext->assignmentMode = AbstractContext::ASSIGNMENT_MODE_VARIABLE;
        $optionsContext->isGlobalReadable = true;
        $optionsContext->isGlobalWritable = false;
        $this->contextStack->push($optionsContext);
        $options = $call->getOptions();
        foreach ($options as $option) {
            $splitTokens = $this->evaluator->queueSplitBy($option->getValue(), Token::T_SEMICOLON);
            foreach ($splitTokens as $tokens) {
                $this->evaluator->evaluate($tokens, $optionsContext);
            }
        }
        $this->contextStack->pop();

        // Testcase Context
        $context = new TestcaseContext(
            $testcase->getDefinition()->getOriginalString(),
            $testcase->getFile(),
            $testcase->getLineNumber()
        );
        $this->contextStack->push($context);

        // Evaluate default options
        $context->assignmentMode = AbstractContext::ASSIGNMENT_MODE_VARIABLE;
        $context->isGlobalReadable = true;
        $context->isGlobalWritable = false;
        $options = $testcase->getDefinition()->getOptions();
        foreach ($options as $option) {
            $splitTokens = $this->evaluator->queueSplitBy($option->getValue(), Token::T_SEMICOLON);
            foreach ($splitTokens as $tokens) {
                $this->evaluator->evaluate($tokens, $context);
            }
        }

        // Prepare and push Testcase Context
        $context->importVariableValues($optionsContext);
        $context->isGlobalReadable = true;

        // Set all parameters
        $arguments = $testcase->getDefinition()->getArguments();
        $parameters = $call->getArguments();
        $argumentsCount = count($arguments);
        if ($argumentsCount !== count($parameters)) {
            throw new RuntimeError('Not enough parameters given to ' . $testcase->getDefinition()->getOriginalString());
        }
        for ($i = 0; $i < $argumentsCount; $i++) {
            $argumentVar = $this->evaluator->extractOperand($arguments[$i]->getValue(), $context);
            if ($parameters[$i]->isByReference()) {
                $parameterVar = $this->evaluator->extractOperand($parameters[$i]->getValue(), $context);
                $reference = $this->contextStack->neck()->getReference($parameterVar);
                $context->setReference($argumentVar, $reference);
            } else {
                $parameterVal = $this->evaluator->evaluate($parameters[$i]->getValue(), $context);
                $context->setVariable($argumentVar, $parameterVal, AbstractContext::ASSIGNMENT_MODE_VARIABLE);
            }
        }

        $requestVarName = new VariableName('$request');
        $responseVarName = new VariableName('$response');
        $this->contextStack->head()->setVariable($requestVarName, new NullLiteral(), AbstractContext::ASSIGNMENT_MODE_VARIABLE);
        $this->contextStack->head()->setVariable($responseVarName, new NullLiteral(), AbstractContext::ASSIGNMENT_MODE_VARIABLE);

        $context->isGlobalWritable = true;
        $this->executeLines($testcase->getLines(), $testcase->getFile(), $testcase->getLineNumber() + 1);

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
        $splitTokens = $this->evaluator->tokenizeSplitBy($expression, Token::T_SEMICOLON);
        $printable = [];
        foreach ($splitTokens as $tokens) {
            $value = $this->evaluator->evaluate($tokens, $this->contextStack->head());
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
        $value = $this->evaluator->evaluate($expression, $this->contextStack->head());
        if (!$value instanceof NumberLiteral) {
            throw new RuntimeError('Sleep required Number of seconds. But ' . $value::TYPE_NAME . ' given');
        }
        usleep((int) ($value->getValue() * 1000000));
    }

    /**
     * @param string $expression
     * @throws RuntimeError
     */
    protected function operatorPause(string $expression)
    {
        $value = $this->evaluator->evaluate($expression, $this->contextStack->head());
        if ($value instanceof NullLiteral) {
            $value = new NumberLiteral(0);
        }
        if (!$value instanceof NumberLiteral) {
            throw new RuntimeError('Pause requires Number of seconds. But ' . $value::TYPE_NAME . ' given');
        }
        In::pressEnter($value->getValue());
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
            if (stripos($line, $operator . ' ') === 0 || strtolower($line) === strtolower($operator)) {
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