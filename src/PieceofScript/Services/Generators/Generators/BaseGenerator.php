<?php

namespace PieceofScript\Services\Generators\Generators;

use PieceofScript\Services\Contexts\ContextStack;
use PieceofScript\Services\Generators\IGenerator;
use PieceofScript\Services\Parsing\Parser;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\VariableName;


abstract class BaseGenerator implements IGenerator
{
    /**
     * Unique id
     * @var string
     */
    protected $name;

    /**
     * @var VariableName[]
     */
    protected $arguments = [];

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

    public function __construct($name, $arguments = [], $fileName = null)
    {
        $this->setName($name);
        $this->setArguments($arguments);
        $this->setFileName($fileName);
    }

    /**
     * @param BaseLiteral[] ...$arguments
     * @return BaseLiteral
     */
    public function run(...$arguments): BaseLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement run()');
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
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     * @return BaseGenerator
     */
    public function setArguments(array $arguments): BaseGenerator
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName ?? 'internal';
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
}