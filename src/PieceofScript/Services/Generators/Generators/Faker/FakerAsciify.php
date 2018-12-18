<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerAsciify extends FakerGenerator
{
    const NAME = 'Faker\\asciify';

    public function run(...$arguments): BaseLiteral
    {
        if (count($arguments) < 1) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }

        return new StringLiteral($this->faker->asciify($arguments[0]->toString()->getValue()));
    }

}