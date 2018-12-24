<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerLocale extends FakerGenerator
{
    const NAME = 'Faker\\locale';

    public function run(...$arguments): BaseLiteral
    {
        return new StringLiteral($this->faker->locale);
    }

}