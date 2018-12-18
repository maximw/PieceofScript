<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NumberLiteral;

class FakerLatitude extends FakerGenerator
{
    const NAME = 'Faker\\latitude';

    public function run(...$arguments): BaseLiteral
    {
        return new NumberLiteral($this->faker->latitude);
    }

}