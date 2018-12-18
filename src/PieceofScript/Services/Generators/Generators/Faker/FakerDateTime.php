<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\DateLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NullLiteral;
use PieceofScript\Services\Values\NumberLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerDateTime extends FakerGenerator
{
    const NAME = 'Faker\\datetime';

    public function run(...$arguments): BaseLiteral
    {
        $min = new \DateTime(Config::get()->getCurrentTimestamp(), Config::get()->getDefaultTimezone());
        $min = $min->modify('-30 years');
        $max = new \DateTime(Config::get()->getCurrentTimestamp(), Config::get()->getDefaultTimezone());

        if (isset($arguments[0])) {
            if ($arguments[0] instanceof NumberLiteral) {
                $min = (new \DateTime())->setTimestamp($arguments[0]->getValue());
            } elseif ($arguments[0] instanceof DateLiteral) {
                $min = $arguments[0]->getValue();
            } elseif ($arguments[0] instanceof StringLiteral) {
                $min = new \DateTime($arguments[0]->getValue(), Config::get()->getDefaultTimezone());
            } elseif ($arguments[0] instanceof NullLiteral) {
            } else {
                throw new ArgumentTypeError(self::NAME, 0, $arguments[0]::NAME);
            }
        }

        if (isset($arguments[1])) {
            if ($arguments[1] instanceof NumberLiteral) {
                $max = (new \DateTime())->setTimestamp($arguments[0]->getValue());
            } elseif ($arguments[1] instanceof DateLiteral) {
                $max = $arguments[1]->getValue();
            } elseif ($arguments[1] instanceof StringLiteral) {
                $max = new \DateTime($arguments[1]->getValue(), Config::get()->getDefaultTimezone());
            } elseif ($arguments[0] instanceof NullLiteral) {
            } else {
                throw new ArgumentTypeError(self::NAME, 1, $arguments[1]::NAME);
            }
        }

        return new DateLiteral($this->faker->dateTimeBetween($min, $max, Config::get()->getDefaultTimezone())->format(DATE_ISO8601));
    }

}