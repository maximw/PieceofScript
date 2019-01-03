<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerColorName extends FakerGenerator
{
    const NAME = 'Faker\\colorName';

    public function run(): BaseLiteral
    {
        return new StringLiteral($this->faker->colorName);
    }

}