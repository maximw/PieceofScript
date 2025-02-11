<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

class Append extends ParametrizedGenerator
{
    const NAME = 'append';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 2) {
            throw new ArgumentsCountError(self::NAME, count($this->arguments), 2);
        }

        $array = $this->arguments[0]->getValue();
        array_push($array, $this->arguments[1]);
        return new ArrayLiteral($array);
    }

}