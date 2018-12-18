<?php


namespace PieceofScript\Services\Values;


use PieceofScript\Services\Errors\TypeErrors\ConversionException;
use PieceofScript\Services\Errors\TypeErrors\IncompatibleTypesOperationException;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\Hierarchy\IScalarValue;

class BoolLiteral extends BaseLiteral implements IScalarValue
{
    const TYPE_NAME = 'Boolean';

    /** @var bool */
    protected $value;

    public function __construct($value = false)
    {
        $this->value = (bool) $value;
    }

    public function toBool(): BoolLiteral
    {
        return new BoolLiteral($this->value);
    }

    public function toNumber(): NumberLiteral
    {
        return new NumberLiteral($this->getValue() ? 1 : 0);
    }

    public function toString(): StringLiteral
    {
        throw new ConversionException(self::TYPE_NAME, StringLiteral::TYPE_NAME);
    }

    public function toDate(): DateLiteral
    {
        throw new ConversionException(self::TYPE_NAME, DateLiteral::TYPE_NAME);
    }

    public function toPrint(): string
    {
        return $this->value ? 'True' : 'False';
    }

    public function oEqual(BaseLiteral $value): BoolLiteral
    {
        if ($value instanceof BoolLiteral) {
            return new BoolLiteral($this->getValue() === $value->getValue());
        }

        throw new IncompatibleTypesOperationException('==', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oGreater(BaseLiteral $value): BoolLiteral
    {
        return new BoolLiteral($this->getValue() > $value->toBool()->getValue());
    }

    public function oLower(BaseLiteral $value): BoolLiteral
    {
        return new BoolLiteral($this->getValue() < $value->toBool()->getValue());
    }

    public function oNotEqual(BaseLiteral $value): BoolLiteral
    {
        return new BoolLiteral($this->getValue() != $value->toBool()->getValue());
    }

    public function oGreaterEqual(BaseLiteral $value): BoolLiteral
    {
        return new BoolLiteral($this->getValue() >= $value->toBool()->getValue());
    }

    public function oLowerEqual(BaseLiteral $value): BoolLiteral
    {
        return new BoolLiteral($this->getValue() <= $value->toBool()->getValue());
    }

    public function oPlus(BaseLiteral $value): BaseLiteral
    {
        return new BoolLiteral($this->getValue() || $value->toBool()->getValue());
    }

    public function oMinus(BaseLiteral $value): BaseLiteral
    {
        throw new IncompatibleTypesOperationException('-', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oMultiply(BaseLiteral $value): BaseLiteral
    {
        return new BoolLiteral($this->getValue() && $value->toBool()->getValue());
    }

    public function oNegative(): BaseLiteral
    {
        throw new IncompatibleTypesOperationException('-', self::TYPE_NAME);
    }

    public function oDivide(BaseLiteral $value): BaseLiteral
    {
        throw new IncompatibleTypesOperationException('/', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oDivideMod(BaseLiteral $value): BaseLiteral
    {
        throw new IncompatibleTypesOperationException('%', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oPower(BaseLiteral $value): BaseLiteral
    {
        throw new IncompatibleTypesOperationException('^', self::TYPE_NAME, $value::TYPE_NAME);
    }

}