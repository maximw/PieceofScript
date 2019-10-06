<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

/**
 * Upper case string
 */
class ToLower extends ParametrizedGenerator
{
    const NAME = 'toUpper';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 1) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }

        /** @var BaseLiteral $this->arguments[0] */
        return new StringLiteral(mb_strtolower($this->arguments[0]->toString(), 'UTF-8'));
    }

}