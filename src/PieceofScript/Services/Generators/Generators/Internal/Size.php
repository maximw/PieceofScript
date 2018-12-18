<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\GeneratorInternalException;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\InternalGenerator;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NumberLiteral;
use PieceofScript\Services\Values\StringLiteral;

/**
 * Return Array size or String length
 */
class Size extends InternalGenerator
{
    const NAME = 'size';

    public function run(...$params): BaseLiteral
    {
        if (count($params) < 1) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }

        if ($params[0] instanceof ArrayLiteral) {
            return new NumberLiteral(count($params[0]));
        }

        if ($params[0] instanceof StringLiteral) {
            return new NumberLiteral(mb_strlen($params[0], 'UTF-8'));
        }

        throw new GeneratorInternalException('Not an array or string');
    }

}