<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NumberLiteral;

class Round extends ParametrizedGenerator
{
    const NAME = 'round';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 1) {
            throw new ArgumentsCountError(self::NAME, count($this->arguments), 1);
        }

        $this->arguments[1] = $this->arguments[1]->toNumber();
        $precision = (int) ($this->arguments[2] ?? new NumberLiteral(0))->getValue();

        return new NumberLiteral(round($this->arguments[0]->getValue(), $precision, PHP_ROUND_HALF_UP));
    }

}