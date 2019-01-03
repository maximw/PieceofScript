<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

class ArrayGenerator extends ParametrizedGenerator
{
    const NAME = 'array';

    public function run(): BaseLiteral
    {
        $array = new ArrayLiteral($this->arguments);
        return $array;
    }

}