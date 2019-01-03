<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\DateLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class DateFormat extends ParametrizedGenerator
{
    const NAME = 'dateFormat';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 1) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }

        if (!$this->arguments[0] instanceof DateLiteral) {
            throw new ArgumentTypeError(self::NAME, 0, $this->arguments[0]::TYPE_NAME, DateLiteral::TYPE_NAME);
        }

        $format = Config::get()->getDefaultDateFormat();
        if (isset($this->arguments[1])) {
            if (!$this->arguments[1] instanceof StringLiteral) {
                throw new ArgumentTypeError(self::NAME, 1, $this->arguments[1]::TYPE_NAME, StringLiteral::TYPE_NAME);
            }
            $format = $this->arguments[1]->getValue();
        }

        return new StringLiteral($this->arguments[0]->getValue()->format($format));
    }

}