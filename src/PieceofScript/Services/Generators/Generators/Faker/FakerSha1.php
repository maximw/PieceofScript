<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerSha1 extends FakerGenerator
{
    const NAME = 'Faker\\sha1';

    public function run(): BaseLiteral
    {
        return new StringLiteral($this->faker->sha1);
    }

}