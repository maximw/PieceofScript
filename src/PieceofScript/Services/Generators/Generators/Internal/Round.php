<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\InternalGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NumberLiteral;

class Round extends InternalGenerator
{
    const NAME = 'round';

    public function run(...$params): BaseLiteral
    {
        if (count($params) < 1) {
            throw new ArgumentsCountError(self::NAME, count($params), 1);
        }

        $params[1] = $params[1]->toNumber();
        $precision = (int) ($params[2] ?? new NumberLiteral(0))->getValue();

        return new NumberLiteral(round($params[0]->getValue(), $precision, PHP_ROUND_HALF_UP));
    }

}