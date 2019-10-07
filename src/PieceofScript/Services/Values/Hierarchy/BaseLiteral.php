<?php


namespace PieceofScript\Services\Values\Hierarchy;


abstract class BaseLiteral extends Operand
{
    const TYPE_NAME = 'Literal';

    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        return $this->value = $value;
    }


}