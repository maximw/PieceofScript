<?php


namespace PieceofScript\Services\Generators\Generators\Internal;

use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\DateLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class DateModify extends ParametrizedGenerator
{
    const NAME = 'dateModify';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 2) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }

        if (!$this->arguments[0] instanceof DateLiteral) {
            throw new ArgumentTypeError(self::NAME, 0, $this->arguments[0]::TYPE_NAME, DateLiteral::TYPE_NAME);
        }

        if (!$this->arguments[1] instanceof StringLiteral && !$this->arguments[1] instanceof DateLiteral) {
            throw new ArgumentTypeError(self::NAME, 1, $this->arguments[1]::TYPE_NAME, StringLiteral::TYPE_NAME);
        }


        if (isset($this->arguments[1])) {
            if (!$this->arguments[1] instanceof StringLiteral) {
                throw new ArgumentTypeError(self::NAME, 1, $this->arguments[1]::TYPE_NAME, StringLiteral::TYPE_NAME);
            }
            $format = $this->arguments[1]->getValue();
        }

        /** @var \DateTime $date */
        if ($this->arguments[1] instanceof StringLiteral) {
            $date = clone $this->arguments[0]->getValue();
            $dateString =  $date->modify($this->arguments[1]->getValue())->format(DATE_ATOM);
        } else {
            $dateString = $this->arguments[1]->getValue()->format(DATE_ATOM);
        }

        return new DateLiteral($dateString);
    }

}