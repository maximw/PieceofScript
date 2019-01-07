<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class Explode extends ParametrizedGenerator
{
    const NAME = 'explode';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 1) {
            throw new ArgumentsCountError(self::NAME, count($this->arguments), 1);
        }

        if (!$this->arguments[0] instanceof StringLiteral) {
            throw new ArgumentTypeError(self::NAME, 0, $this->arguments[0], StringLiteral::TYPE_NAME);
        }

        $separator = '';
        if (isset($this->arguments[1])) {
            if (!$this->arguments[1] instanceof StringLiteral) {
                throw new ArgumentTypeError(self::NAME, 0, $this->arguments[0], StringLiteral::TYPE_NAME);
            }

            $separator = $this->arguments[1]->getValue();
        }

        return new ArrayLiteral(explode($separator, $this->arguments[0]));
    }

}