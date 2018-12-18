<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NullLiteral;
use PieceofScript\Services\Values\NumberLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerPassword extends FakerGenerator
{
    const NAME = 'Faker\\password';

    public function run(...$arguments): BaseLiteral
    {
        $min = 6;
        $max = 20;

        if (isset($arguments[0])) {
            if (!$arguments[0] instanceof NumberLiteral && !$arguments[0] instanceof NullLiteral) {
                throw new ArgumentTypeError(self::NAME, 0, NumberLiteral::TYPE_NAME, $arguments[0]::TYPE_NAME);
            }
            if ($arguments[0] instanceof NumberLiteral) {
                $min = $arguments[0]->getValue();
            }
        }

        if (isset($arguments[1])) {
            if (!$arguments[1] instanceof NumberLiteral && !$arguments[1] instanceof NullLiteral) {
                throw new ArgumentTypeError(self::NAME, 1, NumberLiteral::TYPE_NAME, $arguments[1]::TYPE_NAME);
            }
            if ($arguments[1] instanceof NumberLiteral) {
                $max = $arguments[1]->getValue();
            }
        }

        return new StringLiteral($this->faker->password($min, $max));
    }

}