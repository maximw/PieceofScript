<?php


namespace PieceofScript\Services\Values;


use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Errors\TypeErrors\IncompatibleTypesOperationException;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\Hierarchy\IKeyValue;
use PieceofScript\Services\Values\Hierarchy\IScalarValue;

class NumberLiteral extends BaseLiteral implements IScalarValue, IKeyValue
{
    const TYPE_NAME = 'Number';

    protected $value;

    public function __construct($value = 0)
    {
        $this->value = (float) $value;
    }

    public function toBool(): BoolLiteral
    {
        return new BoolLiteral($this->value != 0);
    }

    public function toNumber(): NumberLiteral
    {
        return new NumberLiteral($this->value);
    }

    public function toString(): StringLiteral
    {
        return new StringLiteral((string) $this->value);
    }

    public function toDate(): DateLiteral
    {
        return new DateLiteral(\DateTimeImmutable::createFromFormat('U.u', $this->value, Config::get()->getDefaultTimezone()));
    }

    public function toPrint(): string
    {
        return (string) $this->value;
    }

    public function toKey()
    {
        return (int) $this->value;
    }

    public function oEqual(BaseLiteral $value): BoolLiteral
    {
        if ($value instanceof NullLiteral) {
            return new BoolLiteral(false);
        }
        if ($value instanceof NumberLiteral) {
            return new BoolLiteral($this->getValue() == $value->getValue());
        }

        throw new IncompatibleTypesOperationException('==', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oGreater(BaseLiteral $value): BoolLiteral
    {
        if ($value instanceof NumberLiteral) {
            return new BoolLiteral($this->getValue() > $value->getValue());
        }

        throw new IncompatibleTypesOperationException('>', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oLower(BaseLiteral $value): BoolLiteral
    {
        if ($value instanceof NumberLiteral) {
            return new BoolLiteral($this->getValue() < $value->getValue());
        }

        throw new IncompatibleTypesOperationException('<', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oNotEqual(BaseLiteral $value): BoolLiteral
    {
        if ($value instanceof NullLiteral) {
            return new BoolLiteral(true);
        }
        if ($value instanceof NumberLiteral) {
            return new BoolLiteral($this->getValue() != $value->getValue());
        }

        throw new IncompatibleTypesOperationException('!=', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oGreaterEqual(BaseLiteral $value): BoolLiteral
    {
        if ($value instanceof NumberLiteral) {
            return new BoolLiteral($this->getValue() >= $value->getValue());
        }

        throw new IncompatibleTypesOperationException('>=', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oLowerEqual(BaseLiteral $value): BoolLiteral
    {
        if ($value instanceof NumberLiteral) {
            return new BoolLiteral($this->getValue() <= $value->getValue());
        }

        throw new IncompatibleTypesOperationException('<=', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oPlus(BaseLiteral $value): BaseLiteral
    {
        if ($value instanceof NumberLiteral) {
            return new NumberLiteral($this->getValue() + $value->getValue());
        }

        throw new IncompatibleTypesOperationException('+', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oMinus(BaseLiteral $value): BaseLiteral
    {
        if ($value instanceof NumberLiteral) {
            return new NumberLiteral($this->getValue() - $value->getValue());
        }

        throw new IncompatibleTypesOperationException('-', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oMultiply(BaseLiteral $value): BaseLiteral
    {
        if ($value instanceof NumberLiteral) {
            return new NumberLiteral($this->getValue() * $value->getValue());
        }

        throw new IncompatibleTypesOperationException('*', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oPositive(): BaseLiteral
    {
        return $this;
    }

    public function oNegative(): BaseLiteral
    {
        return new NumberLiteral(-$this->getValue());
    }

    public function oDivide(BaseLiteral $value): BaseLiteral
    {
        if ($value instanceof NumberLiteral) {
            return new NumberLiteral($this->getValue() / $value->getValue());
        }

        throw new IncompatibleTypesOperationException('/', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oDivideMod(BaseLiteral $value): BaseLiteral
    {
        if ($value instanceof NumberLiteral) {
            return new NumberLiteral(fmod($this->getValue(), $value->getValue()));
        }

        throw new IncompatibleTypesOperationException('%', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oPower(BaseLiteral $value): BaseLiteral
    {
        if ($value instanceof NumberLiteral) {
            return new NumberLiteral($this->getValue() ** $value->getValue());
        }

        throw new IncompatibleTypesOperationException('^', self::TYPE_NAME, $value::TYPE_NAME);
    }

}