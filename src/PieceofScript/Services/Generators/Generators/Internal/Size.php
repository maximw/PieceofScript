<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NumberLiteral;
use PieceofScript\Services\Values\StringLiteral;

/**
 * Return Array size or String length
 */
class Size extends ParametrizedGenerator
{
    const NAME = 'size';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 1) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }

        if ($this->arguments[0] instanceof ArrayLiteral) {
            return new NumberLiteral(count($this->arguments[0]));
        }

        if ($this->arguments[0] instanceof StringLiteral) {
            return new NumberLiteral(mb_strlen($this->arguments[0], 'UTF-8'));
        }

        throw new ArgumentTypeError(self::NAME,0, $this->arguments[0]::TYPE_NAME, [ArrayLiteral::TYPE_NAME, StringLiteral::TYPE_NAME]);
    }

}