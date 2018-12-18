<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerTimezone extends FakerGenerator
{
    const NAME = 'Faker\\timezone';

    public function run(...$arguments): BaseLiteral
    {
        return new StringLiteral($this->faker->timezone);
    }

}