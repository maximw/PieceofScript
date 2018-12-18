<?php


namespace PieceofScript\Services\Values\Hierarchy;


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

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        return $this->value = $value;
    }

    /**
     * Convert value to Boolean
     * @return BoolLiteral
     * @throws \Exception
     */
    public function toBool(): BoolLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Convert value to Number
     * @return NumberLiteral
     * @throws \Exception
     */
    public function toNumber(): NumberLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Convert value to String
     * @return StringLiteral
     * @throws \Exception
     */
    public function toString(): StringLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Convert value to Date
     * @return DateLiteral
     * @throws \Exception
     */
    public function toDate(): DateLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Get printable representation of value
     * @return string
     * @throws \Exception
     */
    public function toPrint(): string
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation ==
     * @param BaseLiteral $value
     * @return BoolLiteral
     * @throws \Exception
     */
    public function oEqual(BaseLiteral $value): BoolLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation >
     * @param BaseLiteral $value
     * @return BoolLiteral
     * @throws \Exception
     */
    public function oGreater(BaseLiteral $value): BoolLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation <
     * @param BaseLiteral $value
     * @return BoolLiteral
     * @throws \Exception
     */
    public function oLower(BaseLiteral $value): BoolLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation !=
     * @param BaseLiteral $value
     * @return BoolLiteral
     * @throws \Exception
     */
    public function oNotEqual(BaseLiteral $value): BoolLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation >=
     * @param BaseLiteral $value
     * @return BoolLiteral
     * @throws \Exception
     */
    public function oGreaterEqual(BaseLiteral $value): BoolLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation <=
     * @param BaseLiteral $value
     * @return BoolLiteral
     * @throws \Exception
     */
    public function oLowerEqual(BaseLiteral $value): BoolLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation +
     * @param BaseLiteral $value
     * @return BaseLiteral
     * @throws \Exception
     */
    public function oPlus(BaseLiteral $value): BaseLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation -
     * @param BaseLiteral $value
     * @return BaseLiteral
     * @throws \Exception
     */
    public function oMinus(BaseLiteral $value): BaseLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation *
     * @param BaseLiteral $value
     * @return BaseLiteral
     * @throws \Exception
     */
    public function oMultiply(BaseLiteral $value): BaseLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation + unary
     * @return BaseLiteral
     * @throws \Exception
     */
    public function oPositive(): BaseLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation - unary
     * @return BaseLiteral
     * @throws \Exception
     */
    public function oNegative(): BaseLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation /
     * @param BaseLiteral $value
     * @return BaseLiteral
     * @throws \Exception
     */
    public function oDivide(BaseLiteral $value): BaseLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation %
     * @param BaseLiteral $value
     * @return BaseLiteral
     * @throws \Exception
     */
    public function oDivideMod(BaseLiteral $value): BaseLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation ^
     * @param BaseLiteral $value
     * @return BaseLiteral
     * @throws \Exception
     */
    public function oPower(BaseLiteral $value): BaseLiteral
    {
        throw new \Exception(get_class($this) . ' have to implement ' . __METHOD__);
    }

    /**
     * Operation ||
     * @param BaseLiteral $value
     * @return BoolLiteral
     * @throws \Exception
     */
    public function oOr(BaseLiteral $value): BoolLiteral
    {
        return new BoolLiteral($this->toBool()->getValue() || $value->toBool()->getValue());
    }

    /**
     * Operation &&
     * @param BaseLiteral $value
     * @return BoolLiteral
     * @throws \Exception
     */
    public function oAnd(BaseLiteral $value): BoolLiteral
    {
        return new BoolLiteral($this->toBool()->getValue() && $value->toBool()->getValue());
    }

    /**
     * Operation !
     * @return BoolLiteral
     * @throws \Exception
     */
    public function oNot(): BoolLiteral
    {
        return new BoolLiteral(!$this->toBool()->getValue());
    }



}