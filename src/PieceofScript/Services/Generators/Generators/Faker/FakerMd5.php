<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerMd5 extends FakerGenerator
{
    const NAME = 'Faker\\md5';

    public function run(...$arguments): BaseLiteral
    {
        return new StringLiteral($this->faker->md5);
    }

}