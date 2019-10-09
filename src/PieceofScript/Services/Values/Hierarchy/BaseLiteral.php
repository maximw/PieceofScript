<?php


namespace PieceofScript\Services\Values\Hierarchy;


use PieceofScript\Services\Errors\RuntimeError;
use PieceofScript\Services\Values\BoolLiteral;
use PieceofScript\Services\Values\DateLiteral;
use PieceofScript\Services\Values\NumberLiteral;
use PieceofScript\Services\Values\StringLiteral;


abstract class BaseLiteral extends Operand
{
    const TYPE_NAME = 'Literal';

    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setValue($value)
    {
        return $this->value = $value;
    }

    /**
     * Convert value to Boolean
     * @return BoolLiteral
     * @throws RuntimeError
     */
    public function toBool(): BoolLiteral
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Convert value to Number
     * @return NumberLiteral
     * @throws RuntimeError
     */
    public function toNumber(): NumberLiteral
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Convert value to String
     * @return StringLiteral
     * @throws RuntimeError
     */
    public function toString(): StringLiteral
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Convert value to Date
     * @return DateLiteral
     * @throws RuntimeError
     */
    public function toDate(): DateLiteral
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Get printable representation of value
     * @return string
     * @throws RuntimeError
     */
    public function toPrint(): string
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation ==
     * @param BaseLiteral $value
     * @return BoolLiteral
     * @throws RuntimeError
     */
    public function oEqual(BaseLiteral $value): BoolLiteral
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation >
     * @param BaseLiteral $value
     * @return BoolLiteral
     * @throws RuntimeError
     */
    public function oGreater(BaseLiteral $value): BoolLiteral
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation <
     * @param BaseLiteral $value
     * @return BoolLiteral
     * @throws RuntimeError
     */
    public function oLower(BaseLiteral $value): BoolLiteral
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation !=
     * @param BaseLiteral $value
     * @return BoolLiteral
     * @throws RuntimeError
     */
    public function oNotEqual(BaseLiteral $value): BoolLiteral
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation >=
     * @param BaseLiteral $value
     * @return BoolLiteral
     * @throws RuntimeError
     */
    public function oGreaterEqual(BaseLiteral $value): BoolLiteral
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation <=
     * @param BaseLiteral $value
     * @return BoolLiteral
     * @throws RuntimeError
     */
    public function oLowerEqual(BaseLiteral $value): BoolLiteral
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation +
     * @param BaseLiteral $value
     * @return BaseLiteral
     * @throws RuntimeError
     */
    public function oPlus(BaseLiteral $value): BaseLiteral
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation -
     * @param BaseLiteral $value
     * @return BaseLiteral
     * @throws RuntimeError
     */
    public function oMinus(BaseLiteral $value): BaseLiteral
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation *
     * @param BaseLiteral $value
     * @return BaseLiteral
     * @throws RuntimeError
     */
    public function oMultiply(BaseLiteral $value): BaseLiteral
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation + unary
     * @return BaseLiteral
     * @throws RuntimeError
     */
    public function oPositive(): BaseLiteral
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation - unary
     * @return BaseLiteral
     * @throws RuntimeError
     */
    public function oNegative(): BaseLiteral
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation /
     * @param BaseLiteral $value
     * @return BaseLiteral
     * @throws RuntimeError
     */
    public function oDivide(BaseLiteral $value): BaseLiteral
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation %
     * @param BaseLiteral $value
     * @return BaseLiteral
     * @throws RuntimeError
     */
    public function oDivideMod(BaseLiteral $value): BaseLiteral
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation ^
     * @param BaseLiteral $value
     * @return BaseLiteral
     * @throws RuntimeError
     */
    public function oPower(BaseLiteral $value): BaseLiteral
    {
        throw new RuntimeError(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation ||
     * @param BaseLiteral $value
     * @return BoolLiteral
     * @throws RuntimeError
     */
    public function oOr(BaseLiteral $value): BoolLiteral
    {
        return new BoolLiteral($this->toBool()->getValue() || $value->toBool()->getValue());
    }

    /**
     * Operation &&
     * @param BaseLiteral $value
     * @return BoolLiteral
     * @throws RuntimeError
     */
    public function oAnd(BaseLiteral $value): BoolLiteral
    {
        return new BoolLiteral($this->toBool()->getValue() && $value->toBool()->getValue());
    }

    /**
     * Operation !
     * @return BoolLiteral
     * @throws RuntimeError
     */
    public function oNot(): BoolLiteral
    {
        return new BoolLiteral(!$this->toBool()->getValue());
    }

}