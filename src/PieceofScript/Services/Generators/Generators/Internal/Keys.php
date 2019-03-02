<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

class Keys extends ParametrizedGenerator
{
    const NAME = 'keys';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 1) {
            throw new ArgumentsCountError(self::NAME, count($this->arguments), 1);
        }

        if (!$this->arguments[0] instanceof ArrayLiteral) {
            throw new ArgumentTypeError(self::NAME, 0, $this->arguments[0]::TYPE_NAME, ArrayLiteral::TYPE_NAME);
        }

        $array = array_keys($this->arguments[0]->getValue());
        return new ArrayLiteral($array);
    }

}