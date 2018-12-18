<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NumberLiteral;

class FakerLongitude extends FakerGenerator
{
    const NAME = 'Faker\\longitude';

    public function run(...$arguments): BaseLiteral
    {
        return new NumberLiteral($this->faker->longitude);
    }

}