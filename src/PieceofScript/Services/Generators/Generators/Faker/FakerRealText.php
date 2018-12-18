<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerRealText extends FakerGenerator
{
    const NAME = 'Faker\\realText';

    public function run(...$arguments): BaseLiteral
    {
        $count = 200;
        if (isset($arguments[0])) {
            $count = $arguments[0]->toNumber()->getValue();
        }

        return new StringLiteral($this->faker->realText($count));
    }

}