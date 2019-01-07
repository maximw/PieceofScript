<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Utils\Utils;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class Implode extends ParametrizedGenerator
{
    const NAME = 'implode';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 1) {
            throw new ArgumentsCountError(self::NAME, count($this->arguments), 1);
        }

        if (!$this->arguments[0] instanceof ArrayLiteral) {
            throw new ArgumentTypeError(self::NAME, 0, $this->arguments[0], ArrayLiteral::TYPE_NAME);
        }
        $array = Utils::unwrapValueContainer($this->arguments[0]);

        $separator = '';
        if (isset($this->arguments[1])) {
            if (!$this->arguments[1] instanceof StringLiteral) {
                throw new ArgumentTypeError(self::NAME, 0, $this->arguments[0], StringLiteral::TYPE_NAME);
            }

            $separator = $this->arguments[1]->getValue();
        }

        return new StringLiteral(implode($separator, $array));
    }

}