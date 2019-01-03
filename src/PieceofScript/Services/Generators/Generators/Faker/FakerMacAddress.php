<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerMacAddress extends FakerGenerator
{
    const NAME = 'Faker\\macAddress';

    public function run(): BaseLiteral
    {
        return new StringLiteral($this->faker->macAddress);
    }

}