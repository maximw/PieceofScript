<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Utils\Utils;
use PieceofScript\Services\Values\DateLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NullLiteral;
use PieceofScript\Services\Values\NumberLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerTimestamp extends FakerGenerator
{
    const NAME = 'Faker\\timestamp';

    public function run(): BaseLiteral
    {
        $max = Utils::getRelativeDateTime();
        if (isset($this->arguments[0])) {
            if ($this->arguments[0] instanceof NumberLiteral) {
                $max = (new \DateTime())->setTimestamp($this->arguments[0]->getValue());
            } elseif ($this->arguments[0] instanceof DateLiteral) {
                $max = $this->arguments[0]->getValue();
            } elseif ($this->arguments[0] instanceof StringLiteral) {
                $max = new \DateTime($this->arguments[0]->getValue(), Config::get()->getDefaultTimezone());
            } elseif ($this->arguments[0] instanceof NullLiteral) {
            } else {
                throw new ArgumentTypeError(self::NAME, 0, $this->arguments[0]::NAME);
            }
        }

        return new NumberLiteral($this->faker->unixTime($max));
    }

}