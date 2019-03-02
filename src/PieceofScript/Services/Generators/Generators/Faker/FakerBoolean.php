<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\BoolLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

class FakerBoolean extends FakerGenerator
{
    const NAME = 'Faker\\boolean';

    public function run(): BaseLiteral
    {
        $trueChance = 50;
        if (isset($this->arguments[0])) {
            $trueChance = (int) max(min($this->arguments[0]->toNumber()->getValue(), 100), 0);
        }

        return new BoolLiteral($this->faker->boolean($trueChance));
    }

}