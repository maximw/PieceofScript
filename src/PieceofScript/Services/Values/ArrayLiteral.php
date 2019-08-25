<?php

namespace PieceofScript\Services\Values;


use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Errors\TypeErrors\ConversionException;
use PieceofScript\Services\Errors\TypeErrors\IncompatibleTypesOperationException;
use PieceofScript\Services\Utils\Utils;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

class ArrayLiteral extends BaseLiteral implements \Iterator, \ArrayAccess, \Countable
{
    const TYPE_NAME = 'Array';

    /** @var array  */
    public $value;

    /** @var int  */
    protected $position = 0;

    public function __construct(array $value = [])
    {
        $this->value = $value;
    }

    public function toBool(): BoolLiteral
    {
        return new BoolLiteral(\count($this->value) !== 0);
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
        $value = Utils::unwrapValueContainer($this);
        return json_encode($value, JSON_PRETTY_PRINT, Config::get()->getJsonMaxDepth());
    }

    public function oEqual(BaseLiteral $value): BoolLiteral
    {
        if ($value instanceof NullLiteral) {
            return new BoolLiteral(false);
        }
        if (!$value instanceof ArrayLiteral) {
            throw new IncompatibleTypesOperationException('==', self::TYPE_NAME, $value::TYPE_NAME);
        }

        $keys1 = array_keys($this->value);
        $keys2 = array_keys($value->getValue());

        if ($keys1 != $keys2) {
            return new BoolLiteral(false);
        }

        $array2 = $value->getValue();

        /** @var BaseLiteral $item */
        foreach ($this->value as $key => $item) {
            if (!$item->oEqual($array2[$key])->getValue()) {
                return new BoolLiteral(false);
            }
        }

        return new BoolLiteral(true);
    }

    public function oGreater(BaseLiteral $value): BoolLiteral
    {
        throw new IncompatibleTypesOperationException('>', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oLower(BaseLiteral $value): BoolLiteral
    {
        throw new IncompatibleTypesOperationException('<', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oNotEqual(BaseLiteral $value): BoolLiteral
    {
        if ($value instanceof NullLiteral) {
            return new BoolLiteral(true);
        }
        if (!$value instanceof ArrayLiteral) {
            throw new IncompatibleTypesOperationException('!=', self::TYPE_NAME, $value::TYPE_NAME);
        }

        return new BoolLiteral(!$this->oEqual($value)->getValue());
    }

    public function oGreaterEqual(BaseLiteral $value): BoolLiteral
    {
        throw new IncompatibleTypesOperationException('>=', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oLowerEqual(BaseLiteral $value): BoolLiteral
    {
        throw new IncompatibleTypesOperationException('<=', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oPlus(BaseLiteral $value): BaseLiteral
    {
        if ($value instanceof ArrayLiteral) {
            return new ArrayLiteral(array_merge($this->getValue(), $value->getValue()));
        }

        throw new IncompatibleTypesOperationException('+', self::TYPE_NAME, $value::TYPE_NAME);
    }

    public function oMinus(BaseLiteral $value): BaseLiteral
    {
        if ($value instanceof ArrayLiteral) {
            return new ArrayLiteral(array_diff($this->getValue(), $value->getValue()));
        }

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
        throw new IncompatibleTypesOperationException('^', self::TYPE_NAME, $value::TYPE_NAME);
    }

    /**
     * \Iterator method
     * @return mixed
     */
    public function current()
    {
        $slice = array_slice($this->value, $this->position, 1);
        return end($slice);
    }

    /**
     * \Iterator method
     * @return mixed
     */
    public function key()
    {
        $slice = array_slice(array_keys($this->value), $this->position, 1);
        return end($slice);

    }

    /**
     * \Iterator method
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * \Iterator method
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * \Iterator method
     * @return bool
     */
    public function valid(): bool
    {
        return $this->position >= 0 && $this->position < count($this->value);
    }

    /**
     * \ArrayAccess method
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->value);
    }

    /**
     * \ArrayAccess method
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->value[$offset];
    }

    /**
     * \ArrayAccess method
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->value[$offset] = $value;
    }

    /**
     * \ArrayAccess method
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->value[$offset]);
    }

    /**
     * \Countable method
     * @return int
     */
    public function count(): int
    {
        return count($this->value);
    }
}