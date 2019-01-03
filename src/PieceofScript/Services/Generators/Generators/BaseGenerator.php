<?php

namespace PieceofScript\Services\Generators\Generators;

use PieceofScript\Services\Contexts\ContextStack;
use PieceofScript\Services\Generators\IGenerator;
use PieceofScript\Services\Parsing\Parser;
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

    /** @var Parser */
    protected $parser;

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
            $this->skipRestArgument();
        }
    }

    protected function hasNextArgument(): bool
    {
        return !$this->ast->isEmpty() && $this->ast->head()->getType() !== Token::TYPE_ARGUMENTS_END;
    }

    protected function getNextArgument(): BaseLiteral
    {
        return $this->parser->extractLiteral($this->parser->executeAST($this->ast, $this->contextStack), $this->contextStack);
    }

    protected function skipNextArgument()
    {
       $this->parser->skipAST($this->ast, $this->contextStack);
    }

    protected function skipRestArgument()
    {
        while($this->hasNextArgument()) {
            $this->parser->skipAST($this->ast, $this->contextStack);
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
     * @param Parser $parser
     * @return BaseGenerator
     */
    public function setParser(Parser $parser): BaseGenerator
    {
        $this->parser = $parser;
        return $this;
    }

    /**
     * @param TokensStack $ast
     */
    public function setAst(TokensStack $ast)
    {
        $this->ast = $ast;
    }

}