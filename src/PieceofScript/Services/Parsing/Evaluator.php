<?php

namespace PieceofScript\Services\Parsing;


use PieceofScript\Services\Contexts\AbstractContext;
use PieceofScript\Services\Contexts\ContextStack;
use PieceofScript\Services\Errors\ContextStackEmptyException;
use PieceofScript\Services\Errors\Parser\EmptyExpressionError;
use PieceofScript\Services\Errors\Parser\EvaluationError;
use PieceofScript\Services\Errors\RuntimeError;
use PieceofScript\Services\Generators\GeneratorsRepository;
use PieceofScript\Services\Utils\Utils;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\BoolLiteral;
use PieceofScript\Services\Values\DateLiteral;
use PieceofScript\Services\Values\Hierarchy\IKeyValue;
use PieceofScript\Services\Values\NullLiteral;
use PieceofScript\Services\Values\NumberLiteral;
use PieceofScript\Services\Values\Hierarchy\Operand;
use PieceofScript\Services\Values\StringLiteral;
use PieceofScript\Services\Values\VariableName;

class Evaluator
{

    protected $lexemes = [
        '~^([a-z][a-z0-9\\\\_]*)(?=\s*\()~iu' => Token::T_GENERATOR_NAME,
        '~^\\$[a-z][a-z0-9_]*~iu' => Token::T_VARIABLE,
        '~^\\@[a-z][a-z0-9_]*~iu' => Token::T_VARIABLE_TYPE,
        '~^\.(([a-z][a-z0-9_]*)|([0-9]+))~iu' => Token::T_ARRAY_KEY,

        '~^"(?:[^"\\\\]|\\\\.)*"~u' => Token::T_STRING_DOUBLE,
        "~^'(?:[^'\\\\]|\\\\.)*'~u" => Token::T_STRING,
        '~^(?:[0-9]+(?:[\.][0-9]+)*)(?:e[+-]?[0-9]+)?~u' => Token::T_NUMBER,
        '~^(true|false)~iu' => Token::T_BOOL,
        '~^null~iu' => Token::T_NULL,

        '~^\\(~u' => Token::T_OPEN_PARENTHESIS,
        '~^\\)~u' => Token::T_CLOSE_PARENTHESIS,
        '~^\\[~u' => Token::T_OPEN_BRACKETS,
        '~^\\]~u' => Token::T_CLOSE_BRACKETS,
        '~^\\,~u' => Token::T_COMMA,
        '~^\\s+~u' => Token::T_SPACE,
        '~^//.*$~u' => Token::T_COMMENT,
        '~^=(?!\=)~u' => Token::T_ASSIGNMENT,
        '~^;~u' => Token::T_SEMICOLON,

        '~^==~u' => Token::T_EQUALS,
        '~^>(?!\=)~u' => Token::T_GREATER_THAN,
        '~^<(?!\=)~u' => Token::T_LOWER_THAN,
        '~^!=~u' => Token::T_NOT_EQUALS,
        '~^>=~u' => Token::T_GREATER_EQUAL,
        '~^<=~u' => Token::T_LOWER_EQUAL,
        '~^\\+~u' => Token::T_PLUS,
        '~^\\-~u' => Token::T_MINUS,
        '~^\\*~u' => Token::T_MULTIPLY,
        '~^\\/~u' => Token::T_DIVIDE,
        '~^\\%~u' => Token::T_DIVIDE_MOD,
        '~^\\^~u' => Token::T_POWER,
        '~^\\|\\|~u' => Token::T_OR,
        '~^&&~u' => Token::T_AND,
        '~^!(?!\=)~u' => Token::T_NOT,
    ];

    /** @var GeneratorsRepository */
    protected $generators;

    /** @var ContextStack */
    protected $contextStack;

    /** @var ExpressionLexer */
    protected $expressionLexer;

    public function __construct(
        GeneratorsRepository $generators,
        ContextStack $contextStack,
        ExpressionLexer $expressionLexer
    )
    {
        $this->generators = $generators;
        $this->contextStack = $contextStack;
        $this->expressionLexer = $expressionLexer;
    }

    /**
     * Evaluate string or array. Entry point of Evaluator
     *
     * @param string|array|TokensQueue|TokensStack|BaseLiteral $value
     * @param AbstractContext $context
     * @return BaseLiteral
     * @throws EmptyExpressionError
     * @throws RuntimeError
     */
    public function evaluate($value, AbstractContext $context): BaseLiteral
    {
        return $this->evaluateInternal($value, $context, false);
    }

    /**
     * Evaluate string or array. Entry point of Evaluator
     *
     * @param string|array|TokensQueue|TokensStack|BaseLiteral $value
     * @param AbstractContext $context
     * @param bool $isInternal
     *
     * @return BaseLiteral
     * @throws ContextStackEmptyException
     * @throws EmptyExpressionError
     * @throws RuntimeError
     */
    public function evaluateInternal($value, AbstractContext $context, bool $isInternal = true): BaseLiteral
    {
        $ast = null;
        if (is_string($value)) {
            $tokens = $this->expressionLexer->tokenize($value);
            $ast =  $this->buildAST($tokens);
        } elseif ($value instanceof TokensQueue) {
            $ast = $this->buildAST($value);
        } elseif ($value instanceof TokensStack) {
            $ast = $value;
        } elseif (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = $this->extractLiteral($this->evaluateInternal($val, $context), $context);
            }
            return Utils::wrapValueContainer($value);
        } elseif ($value instanceof BaseLiteral) {
            return $value;
        }

        if ($ast->isEmpty()) {
            return new NullLiteral();
        }

        $value = $this->extractLiteral($this->executeAST($ast, $context), $context);
        if (!$isInternal && !$ast->isEmpty()) {
            throw new EvaluationError('Syntax error', $ast);
        }

        return $value;
    }

    /**
     * Splits expression to array of TokensQueue by token name
     *
     * @param string $expression
     * @param string $splitTokenName
     * @return TokensQueue[]
     * @throws \Exception
     */
    public function tokenizeSplitBy(string $expression, string $splitTokenName = Token::T_SEMICOLON): array
    {
        $tokens = $this->expressionLexer->tokenize($expression);
        return $this->queueSplitBy($tokens, $splitTokenName);
    }

    /**
     * Splits expression to array of TokensQueue by token name
     *
     * @param TokensQueue $tokens
     * @param string $splitTokenName
     * @return TokensQueue[]
     * @throws RuntimeError
     */
    public function queueSplitBy(TokensQueue $tokens, string $splitTokenName = Token::T_SEMICOLON): array
    {
        $split = [];
        $tokensList = new TokensQueue();
        while(!$tokens->isEmpty()) {
            $token = $tokens->get();
            if ($token->getName() === $splitTokenName) {
                $split[] = $tokensList;
                $tokensList = new TokensQueue();
                continue;
            }
            $tokensList->add($token);
        }
        if (!$tokensList->isEmpty()) {
            $split[] = $tokensList;
        }
        return $split;
    }

    /**
     * Analyze $expression and returns list of all used variables in it
     *
     * @param string $expression
     * @return VariableName[]
     * @throws RuntimeError
     */
    public function getUsedVariables(string $expression): array
    {
        $tokens = $this->expressionLexer->tokenize($expression);
        $variables = [];
        while(!$tokens->isEmpty()) {
            $token = $tokens->pop();
            if ($token->getType() === Token::TYPE_VARIABLE) {
                $variables[] = new VariableName($token->getValue());
            }
        }
        return $variables;
    }

    /**
     * Extract operand from given expression
     *
     * @param string|TokensQueue $expression
     * @param AbstractContext $context
     * @return BaseLiteral
     * @throws RuntimeError
     */
    public function extractOperand($expression, AbstractContext $context): Operand
    {
        if (!$expression instanceof TokensQueue) {
            $expression = $this->expressionLexer->tokenize($expression);
        }
        $ast = $this->buildAST($expression);
        if ($ast->isEmpty()) {
            throw new RuntimeError('Error parsing expression ' . $expression);
        }

        return $this->executeAST($ast, $context);
    }


    /**
     * Do not execute part of AST
     * @param TokensStack $ast
     * @throws RuntimeError
     */
    public function skipAST(TokensStack $ast)
    {
        if ($ast->isEmpty()) {
            throw new RuntimeError('AST is empty');
        }

        $token = $ast->pop();

        if ($token->getType() === Token::TYPE_VALUE) {
            return;
        } elseif ($token->getType() === Token::TYPE_OPERATION) {

            if ($token->getArgumentsCount() > 0) {
                $this->skipAST($ast);
            }
            if ($token->getArgumentsCount() > 1) {
                $this->skipAST($ast);
            }

        } elseif ($token->getType() === Token::TYPE_FUNCTION) {

            while (!$ast->isEmpty() && $ast->head()->getType() !== Token::TYPE_ARGUMENTS_END) {
                $this->skipAST($ast);
            }
            $ast->pop(); //Remove TYPE_ARGUMENTS_END

        } elseif ($token->getType() === Token::TYPE_VARIABLE) {

            while (!$ast->isEmpty() && ($ast->head()->getName() === Token::T_ARRAY_KEY || $ast->head()->getName() === Token::T_ARRAY_SUB_AST)) {
                $ast->pop();
            }

        } elseif ($token->getType() === Token::TYPE_ASSIGNMENT) {

            $this->skipAST($ast);
            $this->skipAST($ast);

        } elseif ($token->getType() == Token::TYPE_ARGUMENTS_END) {

            return $this->skipAST($ast);

        }
    }

    /**
     * Build abstract syntax tree
     *
     * @param TokensQueue $tokens
     * @return TokensStack
     * @throws \Exception
     */
    protected function buildAST(TokensQueue $tokens): TokensStack
    {
        $ast = new TokensStack();
        $stack = new TokensStack();

        while (!$tokens->isEmpty()) {
            $token = $tokens->pop();

            if ($token->getType() === Token::TYPE_VALUE) {

                $ast->push($token);

            } elseif ($token->getType() === Token::TYPE_VARIABLE) {

                $ast->push($token);

            } elseif ($token->getType() === Token::TYPE_FUNCTION) {

                $ast->push($token);

            } elseif ($token->getType() === Token::TYPE_COMMA) {

                while (!$stack->isEmpty() && $stack->head()->getType() !== Token::TYPE_CLOSE_PARENTHESIS) {
                    $ast->push($stack->pop());
                }

            } elseif ($token->getType() === Token::TYPE_OPERATION || $token->getType() === Token::TYPE_ASSIGNMENT) {

                while (!$stack->isEmpty()
                    && ($stack->head()->getType() === Token::TYPE_OPERATION || $stack->head()->getType() === Token::TYPE_ASSIGNMENT || $stack->head()->getType() === Token::TYPE_FUNCTION)
                    && !$token->morePriority($stack->head()))
                {
                    $ast->push($stack->pop());
                }
                $stack->push($token);

            } elseif ($token->getType() === Token::TYPE_CLOSE_PARENTHESIS) {

                $stack->push($token);
                $ast->push($this->getArgumentsEnd($token->getOffset()));

            } elseif ($token->getType() === Token::TYPE_OPEN_PARENTHESIS) {

                while($stack->head()->getType() !== Token::TYPE_CLOSE_PARENTHESIS) {
                    $ast->push($stack->pop());
                }
                $stack->pop();

            } elseif ($token->getType() === Token::TYPE_CLOSE_BRACKETS) {

                $subAst = $this->buildAST($tokens);
                $subToken = new Token(Token::T_ARRAY_SUB_AST, $subAst, $token->getOffset());
                $ast->push($subToken);

            } elseif ($token->getType() === Token::TYPE_OPEN_BRACKETS) {

                while (!$stack->isEmpty()) {
                    $ast->push($stack->pop());
                }
                return $ast;

            } elseif ($token->getType() === Token::TYPE_ARRAY_KEY) {

                $ast->push($token);

            }
        }

        while (!$stack->isEmpty()) {
            $ast->push($stack->pop());
        }

        return $ast;
    }

    /**
     * @param TokensStack $ast
     * @param AbstractContext $context
     * @return Operand
     * @throws EmptyExpressionError
     * @throws RuntimeError
     * @throws ContextStackEmptyException
     */
    protected function executeAST(TokensStack $ast, AbstractContext $context): Operand
    {
        if ($ast->isEmpty()) {
            throw new EmptyExpressionError($context);
        }
        $token = $ast->pop();
        if ($token->getType() === Token::TYPE_VALUE) {
            return $this->getOperand($token, $context);
        } elseif ($token->getType() === Token::TYPE_OPERATION) {
            return $this->executeOperation($token, $ast, $context);
        } elseif ($token->getType() === Token::TYPE_FUNCTION) {
            return $this->executeGenerator($token, $ast, $context);
        } elseif ($token->getType() === Token::TYPE_VARIABLE) {
            return $this->getVariableName($token, $ast, $context);
        } elseif ($token->getType() === Token::TYPE_ASSIGNMENT) {
            return $this->executeAssignment($token, $ast, $context);
        } elseif ($token->getType() == Token::TYPE_ARGUMENTS_END) {
            return $this->executeAST($ast, $context);
        }
        throw new RuntimeError('Evaluating error, unknown token ' . $token->getName());
    }

    /**
     * Get one operand - VariableName or Literal
     *
     * @param Token $token
     * @param AbstractContext $context
     * @return BaseLiteral
     * @throws \Exception
     */
    protected function getOperand(Token $token, AbstractContext $context): Operand
    {
        if ($token->getName() === Token::T_NULL) {

            return new NullLiteral();

        } elseif ($token->getName() === Token::T_BOOL) {

            return new BoolLiteral(strtolower($token->getValue()) === 'true' ? true : false);

        } elseif ($token->getName() === Token::T_STRING_DOUBLE) {

            return new StringLiteral($token->getValue());

        } elseif ($token->getName() === Token::T_STRING ) {

            return new DateLiteral($token->getValue());

        } elseif ($token->getName() === Token::T_NUMBER) {

            return new NumberLiteral($token->getValue());

        }

        throw new RuntimeError('Cannot get value of unknown token type ' . $token->getName());
    }

    /**
     * @param Token $operation
     * @param TokensStack $ast
     * @param AbstractContext $context
     * @return BaseLiteral
     * @throws \Exception
     */
    protected function executeOperation(Token $operation, TokensStack $ast, AbstractContext $context): BaseLiteral
    {
        if ($operation->getArgumentsCount() > 0) {
            $operand1 = $this->extractLiteral($this->executeAST($ast, $context), $context);
        }

        if ($operand1->toBool()->getValue() && $operation->getName() === Token::T_OR) {
            $this->skipAST($ast);
            return new BoolLiteral(true);
        }
        if (!$operand1->toBool()->getValue() && $operation->getName() === Token::T_AND) {
            $this->skipAST($ast);
            return new BoolLiteral(false);
        }

        if ($operation->getArgumentsCount() > 1) {
            $operand2 = $this->extractLiteral($this->executeAST($ast, $context), $context);
        }

        if ($operation->getName() === Token::T_EQUALS) {
            return $operand1->oEqual($operand2);
        } elseif ($operation->getName() === Token::T_GREATER_THAN) {
            return $operand1->oGreater($operand2);
        } elseif ($operation->getName() === Token::T_LOWER_THAN) {
            return $operand1->oLower($operand2);
        } elseif ($operation->getName() === Token::T_NOT_EQUALS) {
            return $operand1->oNotEqual($operand2);
        } elseif ($operation->getName() === Token::T_GREATER_EQUAL) {
            return $operand1->oGreaterEqual($operand2);
        } elseif ($operation->getName() === Token::T_LOWER_EQUAL) {
            return $operand1->oLowerEqual($operand2);
        } elseif ($operation->getName() === Token::T_PLUS) {
            return $operand1->oPlus($operand2);
        } elseif ($operation->getName() === Token::T_MINUS) {
            return $operand1->oMinus($operand2);
        } elseif ($operation->getName() === Token::T_MULTIPLY) {
            return $operand1->oMultiply($operand2);
        } elseif ($operation->getName() === Token::T_POSITIVE) {
            return $operand1->oPositive();
        } elseif ($operation->getName() === Token::T_NEGATIVE) {
            return $operand1->oNegative();
        } elseif ($operation->getName() === Token::T_DIVIDE) {
            return $operand1->oDivide($operand2);
        } elseif ($operation->getName() === Token::T_DIVIDE_MOD) {
            return $operand1->oDivideMod($operand2);
        } elseif ($operation->getName() === Token::T_POWER) {
            return $operand1->oPower($operand2);
        } elseif ($operation->getName() === Token::T_OR) {
            return $operand1->oOr($operand2);
        } elseif ($operation->getName() === Token::T_AND) {
            return $operand1->oAnd($operand2);
        } elseif ($operation->getName() === Token::T_NOT) {
            return $operand1->oNot();
        }

        throw new RuntimeError('Unknown operation ' . $operation->getValue());
    }

    /**
     * @param Token $token
     * @param TokensStack $ast
     * @param AbstractContext $context
     * @return BaseLiteral
     * @throws RuntimeError
     * @throws ContextStackEmptyException
     */
    protected function executeGenerator(Token $token, TokensStack $ast, AbstractContext $context): BaseLiteral
    {
        $generator = $this->generators->get($token->getValue());
        $generator->setEvaluator($this)
                    ->setAst($ast)
                    ->setContext($context)
                    ->setContextStack($this->contextStack);

        $generator->init();
        $value = $generator->run();
        $generator->final();

        return $value;
    }

    /**
     * @param Token $token
     * @param TokensStack $ast
     * @param AbstractContext $context
     * @return VariableName
     * @throws \Exception
     */
    protected function getVariableName(Token $token, TokensStack $ast, AbstractContext $context): VariableName
    {
        $variableName = new VariableName($token->getValue());

        while (!$ast->isEmpty() && ($ast->head()->getName() === Token::T_ARRAY_KEY || $ast->head()->getName() === Token::T_ARRAY_SUB_AST)) {
            $keyToken = $ast->pop();
            /** @var BaseLiteral $key */
            if ($keyToken->getName() === Token::T_ARRAY_KEY) {
                $key = $this->arrayKeyToLiteral($keyToken);
            } elseif ($keyToken->getName() === Token::T_ARRAY_SUB_AST) {
                $key = $this->extractLiteral($this->executeAST($keyToken->getValue(), $context), $context);
            }
            if (!$key instanceof IKeyValue) {
                throw new RuntimeError('Cannot use ' . $key::TYPE_NAME . ' as array key');
            }
            $variableName->addPath($key->toKey());
        }

        return $variableName;
    }

    /**
     * Execute assignment operation
     *
     * @param Token $operationEquals
     * @param TokensStack $ast
     * @param AbstractContext $context
     * @return BaseLiteral
     * @throws ContextStackEmptyException
     */
    protected function executeAssignment(Token $operationEquals, TokensStack $ast, AbstractContext $context): BaseLiteral
    {
        if ($context->assignmentMode === AbstractContext::ASSIGNMENT_MODE_OFF) {
            throw new RuntimeError('Cannot assign value here, did you mean == instead of = ?');
        }

        $variable = $this->executeAST($ast, $context);
        $value = $this->extractLiteral($this->executeAST($ast, $context), $context);

        if (!$variable instanceof VariableName) {
            throw new RuntimeError('Cannot assign value to value, did you mean == instead of = ?');
        }

        $context->setVariable($variable, $value);

        return $value;
    }

    /**
     * Get Literal from Operand, if Operand is VariableName - try to get variable value from variables repository
     *
     * @param Operand $operand
     * @param AbstractContext $context
     * @return BaseLiteral
     * @throws \Exception
     */
    protected function extractLiteral(Operand $operand, AbstractContext $context): BaseLiteral
    {
        if ($operand instanceof BaseLiteral) {
            return $operand;
        }

        if ($operand instanceof VariableName) {
            if ($operand->mode === VariableName::MODE_VALUE ) {
                return $context->getVariable($operand);
            } elseif ($operand->mode === VariableName::MODE_TYPE) {
                return $context->getVariableType($operand);
            }
            throw new RuntimeError('Error parsing variable name');
        }

        throw new RuntimeError('Unknown operand type ' . get_class($operand));
    }

    /**
     * Create Token that means end of argument list for function in AST
     * @param int $offset
     * @return Token
     */
    protected function getArgumentsEnd(int $offset): Token
    {
        $token = new Token(Token::T_ARGUMENTS_END, Token::T_ARGUMENTS_END, $offset);
        return $token;
    }

    protected function arrayKeyToLiteral(Token $token): IKeyValue
    {
        if ($token->getName() !== Token::T_ARRAY_KEY) {
            throw new RuntimeError('Bad token used as array key');
        }

        $value = $token->getValue();
        if (ctype_digit($value)) {
            return new NumberLiteral((int) $value);
        }
        return new StringLiteral($value);
    }
}