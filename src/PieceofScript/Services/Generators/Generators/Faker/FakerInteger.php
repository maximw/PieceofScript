<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NumberLiteral;

class FakerInteger extends FakerGenerator
{
    const NAME = 'Faker\\integer';

    public function run(...$arguments): BaseLiteral
    {
        $min = 0;
        if (isset($arguments[0])) {
            $min = $arguments[0]->toNumber()->getValue();
        }

        $max = PHP_INT_MAX;
        if (isset($arguments[1])) {
            $max = $arguments[1]->toNumber()->getValue();
        }

        return new NumberLiteral($this->faker->numberBetween($min, $max));
    }

}