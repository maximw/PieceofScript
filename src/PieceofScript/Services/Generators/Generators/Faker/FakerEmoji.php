<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerEmoji extends FakerGenerator
{
    const NAME = 'Faker\\emoji';

    public function run(...$arguments): BaseLiteral
    {
        return new StringLiteral($this->faker->emoji());
    }

}