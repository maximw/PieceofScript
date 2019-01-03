<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

/**
 * Converts value to String
 */
class ToString extends ParametrizedGenerator
{
    const NAME = 'toString';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 1) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }

        /** @var BaseLiteral $this->arguments[0] */
        return $this->arguments[0]->toString();
    }

}