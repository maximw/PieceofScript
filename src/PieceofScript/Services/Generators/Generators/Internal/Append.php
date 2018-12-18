<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\InternalGenerator;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

class Append extends InternalGenerator
{
    const NAME = 'append';

    public function run(...$params): BaseLiteral
    {
        if (count($params) < 2) {
            throw new ArgumentsCountError(self::NAME, count($params), 2);
        }

        $array = $params[0]->getValue;
        array_push($array, $params[1]);
        return new ArrayLiteral($array);
    }

}