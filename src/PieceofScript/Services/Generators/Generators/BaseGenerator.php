<?php

namespace PieceofScript\Services\Generators\Generators;

use PieceofScript\Services\Contexts\AbstractContext;
use PieceofScript\Services\Contexts\ContextStack;
use PieceofScript\Services\Generators\IGenerator;
use PieceofScript\Services\Parsing\Evaluator;
use PieceofScript\Services\Parsing\Token;
use PieceofScript\Services\Parsing\TokensStack;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;



abstract class BaseGenerator implements IGenerator
{
    /**
     * Unique id
     * @var string
     */
    protected $name;

    /**
     * File where generator was defined
     *
     * @var string
     */
    protected $fileName;

    /** @var ContextStack */
    protected $contextStack;

    /** @var AbstractContext */
    protected $context;

    /** @var Evaluator */
    protected $evaluator;

    /** @var TokensStack */
    protected $ast;

    /**
     * Was all arguments from AST was extracted end skipped, include TYPE_ARGUMENTS_END token
     *
     * @var bool
     */
    protected $argumentsSkipped = false;

    public function __construct($name, $fileName = null)
    {
        $this->setName($name);
        $this->setFileName($fileName);
    }

    public function init()
    {
        $this->argumentsSkipped = false;
    }

    public function final()
    {
        if (!$this->argumentsSkipped) {
            $this->skipRestArguments();
        }
    }

    protected function hasNextArgument(): bool
    {
        return !$this->ast->isEmpty() && $this->ast->head()->getType() !== Token::TYPE_ARGUMENTS_END;
    }

    protected function getNextArgument(): BaseLiteral
    {
        return $this->evaluator->evaluate($this->ast, $this->context);
    }

    protected function skipNextArgument()
    {
       $this->evaluator->skipAST($this->ast);
    }

    protected function skipRestArguments()
    {
        while($this->hasNextArgument()) {
            $this->evaluator->skipAST($this->ast);
        }
        $this->ast->pop(); //Remove TYPE_ARGUMENTS_END
        $this->argumentsSkipped = true;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return BaseGenerator
     */
    public function setName(string $name): BaseGenerator
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName ?? 'Internal';
    }

    /**
     * @param string $fileName
     * @return BaseGenerator
     */
    public function setFileName(string $fileName = null): BaseGenerator
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @param ContextStack $contextStack
     * @return BaseGenerator
     */
    public function setContextStack(ContextStack $contextStack): BaseGenerator
    {
        $this->contextStack = $contextStack;
        return $this;
    }

    /**
     * @param AbstractContext $context
     * @return BaseGenerator
     */
    public function setContext(AbstractContext $context): BaseGenerator
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @param Evaluator $evaluator
     * @return BaseGenerator
     */
    public function setEvaluator(Evaluator $evaluator): BaseGenerator
    {
        $this->evaluator = $evaluator;
        return $this;
    }

    /**
     * @param TokensStack $ast
     * @return BaseGenerator
     */
    public function setAst(TokensStack $ast): BaseGenerator
    {
        $this->ast = $ast;
        return $this;
    }

}