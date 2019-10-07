<?php

namespace PieceofScript\Services\Contexts;

use PieceofScript\Services\Errors\InternalError;
use PieceofScript\Services\Errors\Parser\VariableError;
use PieceofScript\Services\Errors\RuntimeError;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NullLiteral;
use PieceofScript\Services\Values\StringLiteral;
use PieceofScript\Services\Values\VariableName;
use PieceofScript\Services\Values\VariableReference;
use PieceofScript\Services\Variables\VariablesRepository;

abstract class AbstractContext
{
    const ALLOWED_OPERATORS = [];
    const DISALLOWED_OPERATORS = [];

    const ASSIGNMENT_MODE_OFF = 'off';      // Assignment operator causes error
    const ASSIGNMENT_MODE_CONST = 'const';  // Assignment operator tries to create new constant
    const ASSIGNMENT_MODE_VARIABLE = 'var'; // Assignment operator tries to create new variable

    /**
     * Printable name of object holding Context
     *
     * @var string
     */
    protected $name = '';

    /**
     * Current executing file
     *
     * @var string
     */
    protected $file = '';

    /**
     * Current executing line
     *
     * @var int
     */
    protected $line;


    /** @var VariablesRepository */
    protected $variables;

    /**
     * Link to parent Context
     *
     * @var AbstractContext|null
     */
    protected $parentContext;

    /**
     * Link to child Context
     *
     * @var AbstractContext|null
     */
    protected $childContext;

    /**
     * Link to global Context
     *
     * @var GlobalContext|null
     */
    protected $globalContext;

    /**
     * How assignment operator works now
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

    /**
     * Get value of variable from this or global contexts
     *
     * @param VariableName $variableName
     * @return BaseLiteral
     * @throws VariableError
     */
    public function getVariable(VariableName $variableName): BaseLiteral
    {
        if ($this->variables->existsWithoutPath($variableName)) {
            return $this->variables->get($variableName);
        }

        if ($this->isGlobalReadable && !$this instanceof GlobalContext) {
            return $this->getGlobalContext()->getVariable($variableName);
        }

        throw new VariableError($variableName,'variable not found.');
    }

    /**
     * Get type of variable from this or global contexts
     *
     * @param VariableName $variableName
     * @return BaseLiteral
     * @throws VariableError
     */
    public function getVariableType(VariableName $variableName): BaseLiteral
    {
        if ($this->hasVariable($variableName)) {
            if ($this->hasVariable($variableName, true)) {
                $value = $this->getVariable($variableName);
                return new StringLiteral($value::TYPE_NAME);
            }
            return new NullLiteral();
        }
        if ($this->isGlobalReadable && $this->getGlobalContext()->hasVariable($variableName, true)) {
            $value = $this->getGlobalContext()->getVariable($variableName);
            return new StringLiteral($value::TYPE_NAME);
        }
        return new NullLiteral();
    }

    /**
     * Get reference to variable from this or global contexts
     *
     * @param VariableName $variableName
     * @return VariableReference
     * @throws VariableError
     */
    public function getReference(VariableName $variableName): VariableReference
    {
        if ($this->variables->existsWithoutPath($variableName)) {
            return $this->variables->getReference($variableName);
        }

        if ($this->isGlobalReadable && !($this instanceof GlobalContext)) {
            return $this->getGlobalContext()->getReference($variableName);
        }

        throw new VariableError($variableName,'variable not found.');
    }

    /**
     * Set or add variable to current context
     *
     * @param VariableName $variableName
     * @param BaseLiteral $value
     * @param string|null $assignmentMode
     * @throws VariableError
     */
    public function setVariable(VariableName $variableName, BaseLiteral $value, string $assignmentMode = null)
    {
        if (null === $assignmentMode) { //Do not use current $assignmentMode if it is given (for Global Context)
            $assignmentMode = $this->assignmentMode;
        }

        if ($this->isGlobalWritable && !($this instanceof GlobalContext)) {
            if ($this->variables->exists($variableName, false)) {
                $this->variables->set($variableName, $value, $assignmentMode);
            } else {
                $this->getGlobalContext()->setVariable($variableName, $value, $assignmentMode);
            }
        } else {
            $this->variables->set($variableName, $value, $assignmentMode);
        }
    }

    /**
     * Add named reference to Context variables
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
     * Check if this Context has variable
     *
     * @param VariableName $variableName
     * @param bool $checkPath
     * @return bool
     * @throws VariableError
     */
    public function hasVariable(VariableName $variableName, $checkPath = false): bool
    {
        return $this->variables->exists($variableName, $checkPath);
    }

    /**
     * Makes snapshot of all available variables in Context
     *
     * @param bool $onlyCurrentContext
     * @return VariablesRepository
     * @throws \PieceofScript\Services\Errors\RuntimeError
     */
    public function dumpVariables($onlyCurrentContext = false): VariablesRepository
    {
        $local = $this->variables->getDump();
        if (!$onlyCurrentContext && $this->isGlobalReadable && !$this instanceof GlobalContext) {
            $global = $this->getGlobalContext()->variables->getDump();
            $local->merge($global);
        }
        return $local;
    }

    /**
     * Check if operator allowed in Context
     *
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

    /**
     * Get current executed file name
     *
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * Set current executed file name
     *
     * @param string $file
     * @return AbstractContext
     */
    public function setFile(string $file): self
    {
        $this->file = $file;
        return $this;
    }

    public function getLine()
    {
        return $this->line;
    }

    /**
     * Get current executed line number
     *
     * @param int|null $line
     * @return AbstractContext
     */
    public function setLine(int $line = null): self
    {
        $this->line = $line;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $contextName = ''): self
    {
        $this->name = $contextName;
        return $this;
    }

    /**
     * Return parent Context, where current Context was created
     *
     * @return AbstractContext|null
     */
    public function getParentContext()
    {
        if ($this instanceof GlobalContext) {
            return null;
        }
        return $this->parentContext;
    }

    /**
     * Save link to parent Context
     *
     * @param AbstractContext|null $context
     * @return AbstractContext
     * @throws RuntimeError
     */
    public function setParentContext(AbstractContext $context = null): self
    {
        if (null === $context && !$this instanceof GlobalContext) {
            throw new RuntimeError('Non-global context requires parent context');
        }
        $this->parentContext = $context;
        return $this;
    }

    /**
     * Get child context if it exists
     *
     * @return AbstractContext|null
     */
    public function getChildContext()
    {
        return $this->childContext;
    }

    /**
     * Save link to child Context when it is created
     *
     * @param AbstractContext $context
     * @return AbstractContext
     * @throws \Exception
     */
    public function setChildContext(AbstractContext $context): self
    {
        $this->childContext = $context;
        $context->setParentContext($this);
        $context->setGlobalContext($this->getGlobalContext());
        return $this;
    }

    /**
     * Get lint to global Context
     *
     * @return GlobalContext
     */
    public function getGlobalContext(): GlobalContext
    {
        if ($this instanceof GlobalContext) {
            return $this;
        }
        return $this->globalContext;
    }

    /**
     * Save link to global Context
     *
     * @param GlobalContext|null $context
     * @return AbstractContext
     * @throws InternalError
     */
    public function setGlobalContext(GlobalContext $context = null): self
    {
        if (null === $context && !$this instanceof GlobalContext) {
            throw new InternalError('Non-global context requires global context');
        }

        $this->globalContext = $context;
        return $this;
    }

    /**
     * @return VariablesRepository
     */
    public function getVariables(): VariablesRepository
    {
        return $this->variables;
    }

    /**
     * @param VariablesRepository $variables
     * @return AbstractContext
     */
    public function setVariables(VariablesRepository $variables): AbstractContext
    {
        $this->variables = $variables;
        return $this;
    }

    public function importVariableValues(AbstractContext $context): AbstractContext
    {
        $this->variables->importValues($context->getVariables());
        return $this;
    }
}