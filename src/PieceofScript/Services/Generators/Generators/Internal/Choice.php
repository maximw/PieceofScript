<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\InternalGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NullLiteral;

class Choice extends InternalGenerator
{
    const NAME = 'choice';

    public function run(...$params): BaseLiteral
    {
        if (count($params) < 2) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }

        if (count($params) % 2 == 1) {
            $params[] = new NullLiteral();
        }

        $c = count($params);
        for ($i = 0; $i < $c - 1; $i = $i + 2) {
            if ($params[$i]->toBool()->getValue()) {
                return $params[$i + 1];
            }
        }

        return new NullLiteral();
    }

}