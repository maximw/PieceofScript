<?php


namespace PieceofScript\Services\Variables;


use PieceofScript\Services\Values\Hierarchy\Operand;
use PieceofScript\Services\Values\VariableName;

class Variable
{
    /** @var VariableName */
    protected $name;

    /** @var Operand */
    protected $value;

    /** @var string */
    protected $assignmentMode;

    public function __construct(VariableName $name, Operand $value, string $assignmentMode)
    {
        $this->setName($name);
        $this->setValue($value);
        $this->setAssignmentMode($assignmentMode);
    }

    /**
     * @return VariableName
     */
    public function getName(): VariableName
    {
        return $this->name;
    }

    /**
     * @param VariableName $name
     * @return Variable
     */
    public function setName(VariableName $name): Variable
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Operand
     */
    public function getValue(): Operand
    {
        return $this->value;
    }

    /**
     * @param Operand $value
     * @return Variable
     */
    public function setValue(Operand $value): Variable
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getAssignmentMode(): string
    {
        return $this->assignmentMode;
    }

    /**
     * @param string $assignmentMode
     * @return Variable
     */
    public function setAssignmentMode(string $assignmentMode): Variable
    {
        $this->assignmentMode = $assignmentMode;
        return $this;
    }

}