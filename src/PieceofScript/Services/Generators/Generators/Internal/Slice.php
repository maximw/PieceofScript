<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\GeneratorInternalException;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\InternalGenerator;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NumberLiteral;

/**
 * Return piece of Array
 */
class Slice extends InternalGenerator
{
    const NAME = 'slice';

    public function run(...$params): BaseLiteral
    {
        if (count($params) < 3) {
            throw new ArgumentsCountError(self::NAME, count($params), 3);
        }

        $array = $params[0];
        $offset = $params[1];
        $length = $params[2];
        if (! $array instanceof ArrayLiteral) {
            throw new GeneratorInternalException('Requred array');
        }
        if (! $offset instanceof NumberLiteral) {
            throw new GeneratorInternalException('Requred number');
        }
        if (! $length instanceof NumberLiteral) {
            throw new GeneratorInternalException('Requred number');
        }


        return new ArrayLiteral(array_slice($array->getValue(), (int) $offset->getValue(), (int) $length->getValue()));
    }

}