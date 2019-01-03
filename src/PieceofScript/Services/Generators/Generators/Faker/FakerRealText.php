<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerRealText extends FakerGenerator
{
    const NAME = 'Faker\\realText';

    public function run(): BaseLiteral
    {
        $count = 200;
        if (isset($this->arguments[0])) {
            $count = $this->arguments[0]->toNumber()->getValue();
        }

        return new StringLiteral($this->faker->realText($count));
    }

}