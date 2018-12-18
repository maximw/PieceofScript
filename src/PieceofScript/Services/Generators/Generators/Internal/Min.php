<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\InternalGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

class Min extends InternalGenerator
{
    const NAME = 'min';

    public function run(...$params): BaseLiteral
    {
        if (count($params) < 1) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }

        /** @var BaseLiteral $min */
        $min = $params[0];
        foreach ($params as $param) {
            if ($min->oGreater($param)->getValue()) {
                $min = $param;
            }
        }

        return $min;
    }

}