<?php


namespace PieceofScript\Services\Generators;


use PieceofScript\Services\Contexts\ContextStack;
use PieceofScript\Services\Parsing\Parser;
use PieceofScript\Services\Parsing\TokensStack;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

interface IGenerator
{

    public function init();

    public function run(): BaseLiteral;

    public function final();

    public function getName(): string;

    public function setName(string $name);

    public function setContextStack(ContextStack $contextStack);

    public function setParser(Parser $parser);

    public function setAst(TokensStack $ast);
}