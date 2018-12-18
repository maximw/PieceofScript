<?php


namespace PieceofScript\Services\Generators;


use PieceofScript\Services\Contexts\ContextStack;
use PieceofScript\Services\Parsing\Parser;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

interface IGenerator
{
    /**
     * Run generator
     * @param mixed ...$params
     * @return mixed
     */
    public function run(...$params): BaseLiteral;

    public function getName(): string;

    public function setName(string $name);

    public function getArguments(): array;

    public function setArguments(array $arguments);

    public function setContextStack(ContextStack $contextStack);

    public function setParser(Parser $parser);

}