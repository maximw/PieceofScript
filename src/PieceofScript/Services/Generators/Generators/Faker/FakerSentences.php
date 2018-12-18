<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerSentences extends FakerGenerator
{
    const NAME = 'Faker\\sentences';

    public function run(...$arguments): BaseLiteral
    {
        $count = 1;
        if (isset($arguments[0])) {
            $count = $arguments[0]->toNumber()->getValue();
        }

        return new StringLiteral($this->faker->sentences($count, true));
    }

}