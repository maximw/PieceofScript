<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

class FakerArrayElement extends FakerGenerator
{
    const NAME = 'Faker\\arrayElement';

    public function run(...$arguments): BaseLiteral
    {
        if (count($arguments) < 1) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }

        if (!$arguments[0] instanceof ArrayLiteral) {
            throw new ArgumentTypeError(self::NAME, 0, ArrayLiteral::TYPE_NAME, $arguments[0]::TYPE_NAME);
        }

        $count = 1;
        if (isset($arguments[1])) {
            $count = (int) $arguments[1]->toNumber()->getValue();
        }

        return $this->faker->randomElement($arguments[0]->getValue(), $count);
    }

}