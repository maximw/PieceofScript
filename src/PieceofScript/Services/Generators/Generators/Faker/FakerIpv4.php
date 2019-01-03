<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerIpv4 extends FakerGenerator
{
    const NAME = 'Faker\\ipv4';

    public function run(): BaseLiteral
    {
        return new StringLiteral($this->faker->ipv4);
    }

}