<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerSha256 extends FakerGenerator
{
    const NAME = 'Faker\\sha256';

    public function run(...$arguments): BaseLiteral
    {
        return new StringLiteral($this->faker->sha256);
    }

}