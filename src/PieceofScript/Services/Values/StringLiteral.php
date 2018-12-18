<?php


namespace PieceofScript\Services\Values;


use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Errors\TypeErrors\ConversionException;
use PieceofScript\Services\Errors\TypeErrors\IncompatibleTypesOperationException;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\Hierarchy\IKeyValue;
use PieceofScript\Services\Values\Hierarchy\IScalarValue;

class StringLiteral extends BaseLiteral implements IScalarValue, IKeyValue
{
    const TYPE_NAME = 'String';

    /** @var string */
    protected $value;

    public function __construct($value = '')
    {
        $this->value = (string) $value;
    }

    public function toBool(): BoolLiteral
    {
        return new BoolLiteral($this->value !== '');
    }

    public function toNumber(): NumberLiteral
    {
        return new NumberLiteral(+$this->getValue());
    }

    public function toString(): StringLiteral
    {
        return new StringLiteral($this->getValue());
    }

    public function toDate(): DateLiteral
    {
        try {
            return new DateLiteral(new \DateTimeImmutable($this->value, Config::get()->getDefaultTimezone()));
        } catch (\Exception $e) {
            throw new ConversionException(self::TYPE_NAME, DateLiteral::TYPE_NAME);
        }
    }

    public function toKey()
    {
        return $this->value;
    }

    public function toPrint(): string
    {
        return $this->value;
    }

    public function oEqual(BaseLiteral $value): BoolLiteral
    {
        if ($value instanceof StringLiteral) {
            return new BoolLiteral($this->getValue() === $value->getValue());
        }

        throw new IncompatibleTypesOperationException('==', self::TYPE_NAME, $value::TYPE_NAME);

    }

    public function oGreater(BaseLiteral $value): BoolLiteral
    {
        if ($value instanceof StringLiteral) {
            return new BoolLiteral($this->getValue() > $value->getValue());
        }

        throw new IncompatibleTypesOperationException('>', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oLower(BaseLiteral $value): BoolLiteral
    {
        if ($value instanceof StringLiteral) {
            return new BoolLiteral($this->getValue() < $value->getValue());
        }

        throw new IncompatibleTypesOperationException('<', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oNotEqual(BaseLiteral $value): BoolLiteral
    {
        if ($value instanceof StringLiteral) {
            return new BoolLiteral($this->getValue() != $value->getValue());
        }

        throw new IncompatibleTypesOperationException('!=', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oGreaterEqual(BaseLiteral $value): BoolLiteral
    {
        if ($value instanceof StringLiteral) {
            return new BoolLiteral($this->getValue() >= $value->getValue());
        }

        throw new IncompatibleTypesOperationException('>=', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oLowerEqual(BaseLiteral $value): BoolLiteral
    {
        if ($value instanceof StringLiteral) {
            return new BoolLiteral($this->getValue() <= $value->getValue());
        }

        throw new IncompatibleTypesOperationException('<=', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oPlus(BaseLiteral $value): BaseLiteral
    {
        if ($value instanceof StringLiteral) {
            return new StringLiteral($this->getValue() . $value->getValue());
        }

        throw new IncompatibleTypesOperationException('+', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oMinus(BaseLiteral $value): BaseLiteral
    {
        throw new IncompatibleTypesOperationException('-', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oMultiply(BaseLiteral $value): BaseLiteral
    {
        if ($value instanceof StringLiteral) {
            return new NumberLiteral($this->toNumber()->getValue() * $this->toNumber()->getValue());
        }

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
        throw new IncompatibleTypesOperationException('^', self::TYPE_NAME, $value::TYPE_NAME);
    }
}