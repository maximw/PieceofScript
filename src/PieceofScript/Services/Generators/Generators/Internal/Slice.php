<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NumberLiteral;

/**
 * Return piece of Array
 */
class Slice extends ParametrizedGenerator
{
    const NAME = 'slice';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 3) {
            throw new ArgumentsCountError(self::NAME, count($this->arguments), 3);
        }

        $array = $this->arguments[0];
        $offset = $this->arguments[1];
        $length = $this->arguments[2];
        if (! $array instanceof ArrayLiteral) {
            throw new ArgumentTypeError(self::NAME, 0, $array::TYPE_NAME, ArrayLiteral::TYPE_NAME);
        }
        if (! $offset instanceof NumberLiteral) {
            throw new ArgumentTypeError(self::NAME, 1, $offset::TYPE_NAME, NumberLiteral::TYPE_NAME);
        }
        if (! $length instanceof NumberLiteral) {
            throw new ArgumentTypeError(self::NAME, 2, $length::TYPE_NAME, NumberLiteral::TYPE_NAME);
        }


        return new ArrayLiteral(array_slice($array->getValue(), (int) $offset->getValue(), (int) $length->getValue()));
    }

}