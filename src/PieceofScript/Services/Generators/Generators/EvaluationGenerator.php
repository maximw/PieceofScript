<?php


namespace PieceofScript\Services\Generators\Generators;


use PieceofScript\Services\Parsing\Token;
use PieceofScript\Services\Parsing\TokensStack;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

abstract class EvaluationGenerator extends InternalGenerator
{
    /**
     * @var TokensStack
     */
    protected $ast;

    /**
     * @return TokensStack
     */
    public function getAst(): TokensStack
    {
        return $this->ast;
    }

    /**
     * @param TokensStack $ast
     */
    public function setAst(TokensStack $ast)
    {
        $this->ast = $ast;
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

    protected function skipAllArguments()
    {
        while($this->hasNextArgument()) {
            $this->parser->skipAST($this->ast, $this->contextStack);
        }
    }

}