<?php


namespace PieceofScript\Services\Values;


use PieceofScript\Services\Errors\TypeErrors\ConversionException;
use PieceofScript\Services\Errors\TypeErrors\IncompatibleTypesOperationException;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\Hierarchy\IScalarValue;

class NullLiteral extends BaseLiteral implements IScalarValue
{
    const TYPE_NAME = 'Null';

    protected $value;

    public function __construct()
    {
        $this->value = null;
    }

    public function toBool(): BoolLiteral
    {
        return new BoolLiteral(false);
    }

    public function toNumber(): NumberLiteral
    {
        throw new ConversionException(self::TYPE_NAME, NumberLiteral::TYPE_NAME);
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
        return 'Null';
    }

    public function oEqual(BaseLiteral $value): BoolLiteral
    {
        return new BoolLiteral($value instanceof NullLiteral);
    }

    public function oGreater(BaseLiteral $value): BoolLiteral
    {
        return new BoolLiteral(false);
    }

    public function oLower(BaseLiteral $value): BoolLiteral
    {
        return new BoolLiteral(true);
    }

    public function oNotEqual(BaseLiteral $value): BoolLiteral
    {
        return new BoolLiteral(!$this->oEqual($value)->getValue());
    }

    public function oGreaterEqual(BaseLiteral $value): BoolLiteral
    {
        return $this->oEqual($value);
    }

    public function oLowerEqual(BaseLiteral $value): BoolLiteral
    {
        return new BoolLiteral(true);
    }

    public function oPlus(BaseLiteral $value): BaseLiteral
    {
        throw new IncompatibleTypesOperationException('+', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oMinus(BaseLiteral $value): BaseLiteral
    {
        throw new IncompatibleTypesOperationException('-', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oMultiply(BaseLiteral $value): BaseLiteral
    {
        throw new IncompatibleTypesOperationException('*', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oPositive(): BaseLiteral
    {
        throw new IncompatibleTypesOperationException('+', self::TYPE_NAME);
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
        throw new IncompatibleTypesOperationException('*', self::TYPE_NAME, $value::TYPE_NAME);
    }

}