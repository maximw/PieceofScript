<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\BoolLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

class Regex extends ParametrizedGenerator
{
    const NAME = 'regex';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 2) {
            throw new ArgumentsCountError(self::NAME, count($this->arguments), 2);
        }

        if (preg_match($this->arguments[1]->toString()->getValue(), $this->arguments[0]->toString()->getValue())) {
            return new BoolLiteral(true);
        }

        return new BoolLiteral(false);
    }

}