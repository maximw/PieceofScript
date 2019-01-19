<?php

namespace PieceofScript\Services\Parsing;


use PieceofScript\Services\Contexts\AbstractContext;
use PieceofScript\Services\Contexts\ContextStack;
use PieceofScript\Services\Contexts\GeneratorContext;
use PieceofScript\Services\Errors\Parser\EmptyExpressionError;
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

class Parser
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

    public function __construct(
        GeneratorsRepository $generators,
        ContextStack $contextStack
    )
    {
        $this->generators = $generators;
        $this->contextStack = $contextStack;
    }



    /**
     * Evaluate string or array. Entry point of Parser
     *
     * @param string|array|TokensQueue|TokensStack|BaseLiteral $value
     * @param AbstractContext $context
     * @return BaseLiteral
     */
    public function evaluate($value, AbstractContext $context): BaseLiteral
    {
        if (is_string($value)) {
            $tokens = $this->tokenize($value);
            $ast =  $this->buildAST($tokens);
            return $this->extractLiteral($this->executeAST($ast, $context), $context);
        } elseif ($value instanceof TokensQueue) {
            $ast = $this->buildAST($value);
            return $this->extractLiteral($this->executeAST($ast, $context), $context);
        } elseif ($value instanceof TokensStack) {
            return $this->extractLiteral($this->executeAST($value, $context), $context);
        } elseif (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = $this->extractLiteral($this->evaluate($val, $context), $context);
            }
        }
        return Utils::wrapValueContainer($value);
    }

    /**
     * Splits expression to array of TokensQueue by token name
     *
     * @param string $expression
     * @param string $splitTokenName
     * @return TokensQueue[]
     * @throws \Exception
     */
    public function tokenizeSplitBy(string $expression, string $splitTokenName): array
    {
        $tokens = $this->tokenize($expression);
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
     */
    public function getUsedVariables(string $expression): array
    {
        $tokens = $this->tokenize($expression);
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
     * @throws \Exception
     */
    public function extractOperand($expression, AbstractContext $context): Operand
    {
        if (!$expression instanceof TokensQueue) {
            $expression = $this->tokenize($expression);
        }
        $ast = $this->buildAST($expression);
        //$ast->debug();
        if ($ast->isEmpty()) {
            //$value = $expression;
            throw new \Exception('Error parsing expression ' . $expression);
        }

        return $this->executeAST($ast, $context);
    }


    /**
     * Do not execute part of AST
     * @param TokensStack $ast
     * @throws \Exception
     */
    public function skipAST(TokensStack $ast)
    {
        if ($ast->isEmpty()) {
            throw new \Exception('AST is empty');
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
     * Split string expression to lexical tokens
     *
     * @param string $expression
     * @return TokensQueue
     * @throws \Exception
     */
    protected function tokenize(string $expression): TokensQueue
    {
        $tokens = new TokensQueue();
        $offset = 0;
        $matches = null;
        $expressionCopy = $expression;
        while (mb_strlen($expressionCopy, 'UTF-8')) {
            $anyMatch = false;
            foreach ($this->lexemes as $regex => $lexemeName) {
                if (preg_match($regex, $expressionCopy, $matches)) {
                    $value = $matches[0];
                    $len = mb_strlen($value, 'UTF-8');

                    if (Token::TYPES[$lexemeName] !== Token::TYPE_IGNORED) {
                        if ($lexemeName === Token::T_STRING_DOUBLE) {
                            $value = trim($value, '"');
                        } elseif ($lexemeName === Token::T_STRING) {
                            $value = trim($value, "'");
                        } elseif ($lexemeName === Token::T_ARRAY_KEY) {
                            $value = ltrim($value, '.');
                        }

                        // Unary positive and negative
                        if ($tokens->isEmpty()
                            || $tokens->head()->getName() === Token::T_OPEN_PARENTHESIS
                            || $tokens->head()->getName() === Token::T_OPEN_BRACKETS
                            || $tokens->head()->getName() === Token::T_COMMA
                            || $tokens->head()->getType() === Token::TYPE_OPERATION
                            || $tokens->head()->getType() === Token::TYPE_ASSIGNMENT
                        ) {
                            if ($lexemeName === Token::T_MINUS) {
                                $lexemeName = Token::T_NEGATIVE;
                            }
                            if ($lexemeName === Token::T_PLUS) {
                                $lexemeName = Token::T_POSITIVE;
                            }
                        }

                        $token = new Token($lexemeName, $value, $offset);
                        $tokens->add($token);
                    }

                    $expressionCopy = mb_substr($expressionCopy, $len, null, 'UTF-8');
                    $anyMatch = true;
                    $offset += $len;
                    break;
                }
            }
            if (!$anyMatch) {
                throw new RuntimeError(sprintf('Cannot parse expression at offset %s: %s', $offset, $expression));
            }
        }

        return $tokens;
    }

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
        throw new \Exception('Evaluating error');
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

        throw new \Exception('Cannot get value of unknown token type');
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

        throw new \Exception('Unknown operation ' . $operation->getValue());
    }

    /**
     * @param Token $token
     * @param TokensStack $ast
     * @param AbstractContext $context
     * @return BaseLiteral
     * @throws RuntimeError
     * @throws \PieceofScript\Services\Errors\ContextStackEmptyException
     */
    protected function executeGenerator(Token $token, TokensStack $ast, AbstractContext $context): BaseLiteral
    {
        $generator = $this->generators->get($token->getValue());
        $generator->setParser($this)
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
                throw new \Exception('Cannot use ' . $key::TYPE_NAME . ' as array key');
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
     * @throws \PieceofScript\Services\Errors\ContextStackEmptyException
     */
    protected function executeAssignment(Token $operationEquals, TokensStack $ast, AbstractContext $context): BaseLiteral
    {
        if ($context->assignmentMode === AbstractContext::ASSIGNMENT_MODE_OFF) {
            throw new \Exception('Cannot assign value here, did you mean == instead of = ?');
        }

        $variable = $this->executeAST($ast, $context);
        $value = $this->extractLiteral($this->executeAST($ast, $context), $context);

        if (!$variable instanceof VariableName) {
            throw new \Exception('Cannot assign value to value, did you mean == instead of = ?');
        }

        if ($context->isGlobalWritable) {
            $context->setVariableOrGlobal($variable, $value);
        } else {
            $context->setVariable($variable, $value);
        }

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

                if ($context->hasVariable($operand)) {
                    if ($context->hasVariable($operand, true)) {
                        $value = $context->getVariable($operand);
                        return new StringLiteral($value::TYPE_NAME);
                    }
                    return new BoolLiteral(false);
                }
                if ($context->getGlobalContext()->hasVariable($operand, true)) {
                    $value = $context->getGlobalContext()->getVariable($operand);
                    return new StringLiteral($value::TYPE_NAME);
                }
                return new BoolLiteral(false);

            }

            throw new \Exception('Error variable name');
        }

        throw new \Exception('Unknown operand type ' . get_class($operand));
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
            throw new \Exception('Bad token used as array key');
        }

        $value = $token->getValue();
        if (ctype_digit($value)) {
            return new NumberLiteral((int) $value);
        }
        return new StringLiteral($value);
    }
}