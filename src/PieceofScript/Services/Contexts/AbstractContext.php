<?php

namespace PieceofScript\Services\Contexts;

use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\VariableName;
use PieceofScript\Services\Values\VariableReference;
use PieceofScript\Services\Variables\VariablesRepository;

abstract class AbstractContext
{
    const ALLOWED_OPERATORS = [];
    const DISALLOWED_OPERATORS = [];

    const ASSIGNMENT_MODE_OFF = 'off';
    const ASSIGNMENT_MODE_CONST = 'const';
    const ASSIGNMENT_MODE_VARIABLE = 'var';

    /**
     * Printable name of object holding Context
     *
     * @var string
     */
    public $name = '';

    /**
     * Current executing file
     *
     * @var string
     */
    public $file = '';

    /**
     * Current executing line
     *
     * @var int
     */
    public $line;


    /** @var VariablesRepository */
    protected $variables;

    /**
     * Link to parent Context
     *
     * @var AbstractContext|null
     */
    protected $parentContext;

    /**
     * Link to global Context
     *
     * @var GlobalContext|null
     */
    protected $globalContext;

    /**
     * Can assignment operator be used now
     *
     * @var bool
     */
    public $assignmentMode = self::ASSIGNMENT_MODE_OFF;

    /**
     * Flag read global context if variable does not exists
     *
     * @var bool
     */
    public $isGlobalReadable = true;

    /**
     * Flag write global context if variable does not exists
     *
     * @var bool
     */
    public $isGlobalWritable = true;

    public function __construct(
        string $name,
        string $file = '',
        int $line = null
    )
    {
        $this->setName($name);
        $this->setFile($file);
        $this->setLine($line);

        $this->variables = new VariablesRepository();
    }


    public function getVariable(VariableName $variableName): BaseLiteral
    {
        if ($this->variables->existsWithoutPath($variableName)) {
            return $this->variables->get($variableName);
        }

        if ($this->isGlobalReadable && !$this instanceof GlobalContext) {
            return $this->getGlobalContext()->getVariable($variableName);
        }

        throw new \Exception('Variable not found ' . $variableName);
    }

    public function getReference(VariableName $variableName): VariableReference
    {
        if ($this->variables->existsWithoutPath($variableName)) {
            return $this->variables->getReference($variableName);
        }

        if (!$this->isGlobal()) {
            return $this->getGlobalContext()->getReference($variableName);
        }

        throw new \Exception('Variable not found ' . $variableName);
    }

    /**
     * Set or add variable to current context
     * @param VariableName $variableName
     * @param BaseLiteral $value
     * @param string|null $assignmentMode
     */
    public function setVariable(VariableName $variableName, BaseLiteral $value, string $assignmentMode = null)
    {
        if (null === $assignmentMode) { //Do not use current $assignmentMode if it is given (for Global Context)
            $assignmentMode = $this->assignmentMode;
        }
        $this->variables->set($variableName, $value, $assignmentMode);
    }

    /**
     * Set or add variable to current or global context
     * @param VariableName $variableName
     * @param BaseLiteral $value
     * @param string|null $assignmentMode
     */
    public function setVariableGlobal(VariableName $variableName, BaseLiteral $value, string $assignmentMode = null)
    {
        if (null === $assignmentMode) { //Do not use current $assignmentMode if it is given (for Global Context)
            $assignmentMode = $this->assignmentMode;
        }

        if ($this->variables->exists($variableName, false)) {
            $this->variables->set($variableName, $value, $assignmentMode);
        } else {
            $this->getGlobalContext()->setVariable($variableName, $value, $assignmentMode);
        }
    }

    /**
     * Add named reference to context variables
     *
     * @param VariableName $varName
     * @param VariableReference $reference
     * @throws \Exception
     */
    public function setReference(VariableName $varName, VariableReference $reference)
    {
        $this->variables->setReference($varName, $reference);
    }

    /**
     * Check if context has variable
     *
     * @param VariableName $variableName
     * @param bool $checkPath
     * @return bool
     */
    public function hasVariable(VariableName $variableName, $checkPath = false): bool
    {
        return $this->variables->exists($variableName, $checkPath);
    }

    /**
     * Check if operator allowed in Context
     * @param $operator
     * @return bool
     */
    public  function isAllowedOperator($operator): bool
    {
        if (!empty(static::DISALLOWED_OPERATORS) && in_array($operator, static::DISALLOWED_OPERATORS)) {
            return false;
        }

        if (!empty(static::ALLOWED_OPERATORS) && !in_array($operator, static::ALLOWED_OPERATORS)) {
            return false;
        }

        return true;
    }

    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;
        return $this;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function setLine(int $line = null): self
    {
        $this->line = $line;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $contextName = ''): self
    {
        $this->name = $contextName;
        return $this;
    }

    public function getParentContext()
    {
        if ($this instanceof GlobalContext) {
            return null;
        }
        return $this->parentContext;
    }

    public function setParentContext(AbstractContext $context = null): self
    {
        if (null === $context && !$this instanceof GlobalContext) {
            throw new \Exception('Non-global context requires parent context');
        }
        $this->parentContext = $context;
        return $this;
    }

    public function getGlobalContext(): GlobalContext
    {
        if ($this instanceof GlobalContext) {
            return $this;
        }
        return $this->globalContext;
    }

    public function setGlobalContext(GlobalContext $context = null): self
    {
        if (null === $context && !$this instanceof GlobalContext) {
            throw new \Exception('Non-global context requires global context');
        }

        $this->globalContext = $context;
        return $this;
    }

}