<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\InternalGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NullLiteral;

class IfGenerator extends InternalGenerator
{
    const NAME = 'if';

    public function run(...$params): BaseLiteral
    {
        if (count($params) < 2) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }

        return $params[0]->toBool()->getValue() ? $params[1] : (isset($params[2]) ? $params[2] : new NullLiteral());
    }

}