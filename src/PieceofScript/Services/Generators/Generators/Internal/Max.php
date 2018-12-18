<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\InternalGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

class Max extends InternalGenerator
{
    const NAME = 'max';

    public function run(...$params): BaseLiteral
    {
        if (count($params) < 1) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }

        /** @var BaseLiteral $max */
        $max = $params[0];
        foreach ($params as $param) {
            if ($max->oLower($param)->getValue()) {
                $max = $param;
            }
        }

        return $max;
    }

}