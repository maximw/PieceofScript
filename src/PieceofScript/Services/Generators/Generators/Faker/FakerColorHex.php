<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerColorHex extends FakerGenerator
{
    const NAME = 'Faker\\colorHex';

    public function run(): BaseLiteral
    {
        return new StringLiteral($this->faker->hexColor);
    }

}