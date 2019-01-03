<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

class Prepend extends ParametrizedGenerator
{
    const NAME = 'prepend';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 2) {
            throw new ArgumentsCountError(self::NAME, count($this->arguments), 2);
        }

        $array = $this->arguments[0]->getValue;
        array_unshift($array, $this->arguments[1]);
        return new ArrayLiteral($array);
    }

}