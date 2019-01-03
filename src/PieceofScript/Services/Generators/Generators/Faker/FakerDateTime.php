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

    public function run(): BaseLiteral
    {
        $min = new \DateTime(Config::get()->getCurrentTimestamp(), Config::get()->getDefaultTimezone());
        $min = $min->modify('-30 years');
        $max = new \DateTime(Config::get()->getCurrentTimestamp(), Config::get()->getDefaultTimezone());

        if (isset($this->arguments[0])) {
            if ($this->arguments[0] instanceof NumberLiteral) {
                $min = (new \DateTime())->setTimestamp($this->arguments[0]->getValue());
            } elseif ($this->arguments[0] instanceof DateLiteral) {
                $min = $this->arguments[0]->getValue();
            } elseif ($this->arguments[0] instanceof StringLiteral) {
                $min = new \DateTime($this->arguments[0]->getValue(), Config::get()->getDefaultTimezone());
            } elseif ($this->arguments[0] instanceof NullLiteral) {
            } else {
                throw new ArgumentTypeError(self::NAME, 0, $this->arguments[0]::NAME);
            }
        }

        if (isset($this->arguments[1])) {
            if ($this->arguments[1] instanceof NumberLiteral) {
                $max = (new \DateTime())->setTimestamp($this->arguments[1]->getValue());
            } elseif ($this->arguments[1] instanceof DateLiteral) {
                $max = $this->arguments[1]->getValue()->format(DATE_ISO8601);
            } elseif ($this->arguments[1] instanceof StringLiteral) {
                $max = new \DateTime($this->arguments[1]->getValue(), Config::get()->getDefaultTimezone());
            } elseif ($this->arguments[1] instanceof NullLiteral) {
            } else {
                throw new ArgumentTypeError(self::NAME, 1, $this->arguments[1]::NAME);
            }
        }

        return new DateLiteral($this->faker->dateTimeBetween(
            $min->format(DATE_ISO8601),
            $max->format(DATE_ISO8601),
            Config::get()->getDefaultTimezone()->getName())->format(DATE_ISO8601)
        );
    }

}