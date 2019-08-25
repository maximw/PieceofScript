<?php

namespace PieceofScript\Services\Contexts;


use PieceofScript\Services\Errors\Parser\VariableError;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NullLiteral;
use PieceofScript\Services\Values\StringLiteral;
use PieceofScript\Services\Values\VariableName;
use PieceofScript\Services\Values\VariableReference;

class OptionsContext extends AbstractContext
{

    /**
     * Get value of variable from this, parent or global contexts
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

        if ($this->getParentContext()->variables->existsWithoutPath($variableName)) {
            return $this->getParentContext()->variables->get($variableName);
        }

        if ($this->isGlobalReadable && !$this instanceof GlobalContext) {
            return $this->getGlobalContext()->getVariable($variableName);
        }

        throw new VariableError($variableName,'variable not found.');
    }


    /**
     * Get type of variable from this, parent or global contexts
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
        if ($this->getParentContext()->hasVariable($variableName)) {
            if ($this->getParentContext()->hasVariable($variableName, true)) {
                $value = $this->getParentContext()->getVariable($variableName);
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

    public function getReference(VariableName $variableName): VariableReference
    {
        throw new \Exception('Cannot get reference in OptionsContext');
    }

    /**
     * Set or add variable to current context
     *
     * @param VariableName $variableName
     * @param BaseLiteral $value
     * @param string|null $assignmentMode
     */
    public function setVariable(VariableName $variableName, BaseLiteral $value, string $assignmentMode = null)
    {
        $this->variables->set($variableName, $value, AbstractContext::ASSIGNMENT_MODE_VARIABLE);
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
        throw new \Exception('Cannot set reference in OptionsContext');
    }

}