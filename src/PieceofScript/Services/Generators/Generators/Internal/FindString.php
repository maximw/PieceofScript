<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\BoolLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NumberLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FindString extends ParametrizedGenerator
{
    const NAME = 'findString';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 2) {
            throw new ArgumentsCountError(self::NAME, count($this->arguments), 2);
        }

        if (!$this->arguments[0] instanceof StringLiteral) {
            throw new ArgumentTypeError(self::NAME, 0, $this->arguments[0]::TYPE_NAME, StringLiteral::TYPE_NAME);
        }
        if (!$this->arguments[1] instanceof StringLiteral) {
            throw new ArgumentTypeError(self::NAME, 1, $this->arguments[1]::TYPE_NAME, StringLiteral::TYPE_NAME);
        }
        if (isset($this->arguments[2])) {
            if (!$this->arguments[2] instanceof NumberLiteral) {
                throw new ArgumentTypeError(self::NAME, 2, $this->arguments[2]::TYPE_NAME, NumberLiteral::TYPE_NAME);
            }

            $offset = $this->arguments[2]->getValue();
        } else {
            $offset = 0;
        }

        $position = strpos($this->arguments[1]->getValue(), $this->arguments[0]->getValue(), $offset);
        return $position === false ? new BoolLiteral(false) : new NumberLiteral($position);
    }

}