<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class ReplaceString extends ParametrizedGenerator
{
    const NAME = 'replaceString';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 3) {
            throw new ArgumentsCountError(self::NAME, count($this->arguments), 3);
        }

        if (!$this->arguments[0] instanceof StringLiteral) {
            throw new ArgumentTypeError(self::NAME, 0, $this->arguments[0]::TYPE_NAME, StringLiteral::TYPE_NAME);
        }
        if (!$this->arguments[1] instanceof StringLiteral) {
            throw new ArgumentTypeError(self::NAME, 1, $this->arguments[1]::TYPE_NAME, StringLiteral::TYPE_NAME);
        }
        if (!$this->arguments[2] instanceof StringLiteral) {
            throw new ArgumentTypeError(self::NAME, 2, $this->arguments[2]::TYPE_NAME, StringLiteral::TYPE_NAME);
        }

        $str = substr_replace($this->arguments[0]->getValue(), $this->arguments[1]->getValue(), $this->arguments[2]->getValue());
        return new StringLiteral($str);
    }

}