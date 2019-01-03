<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\InternalGenerator;
use PieceofScript\Services\Values\DateLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class DateFormat extends InternalGenerator
{
    const NAME = 'dateFormat';

    public function run(...$params): BaseLiteral
    {
        if (count($params) < 1) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }

        if (!$params[0] instanceof DateLiteral) {
            throw new ArgumentTypeError(self::NAME, 0, $params[0]::TYPE_NAME, DateLiteral::TYPE_NAME);
        }

        $format = Config::get()->getDefaultDateFormat();
        if (isset($params[1])) {
            if (!$params[1] instanceof StringLiteral) {
                throw new ArgumentTypeError(self::NAME, 1, $params[1]::TYPE_NAME, StringLiteral::TYPE_NAME);
            }
            $format = $params[1]->getValue();
        }

        return new StringLiteral($params[0]->getValue()->format($format));
    }

}