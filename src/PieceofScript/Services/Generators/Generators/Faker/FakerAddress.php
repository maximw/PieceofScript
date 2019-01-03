<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerAddress extends FakerGenerator
{
    const NAME = 'Faker\\address';

    public function run(): BaseLiteral
    {
        return new StringLiteral($this->faker->address());
    }

}