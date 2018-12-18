<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Generators\Generators\InternalGenerator;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

class ArrayGenerator extends InternalGenerator
{
    const NAME = 'array';

    public function run(...$params): BaseLiteral
    {
        $array = new ArrayLiteral($params);
        return $array;
    }

}