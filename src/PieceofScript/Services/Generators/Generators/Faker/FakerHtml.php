<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerHtml extends FakerGenerator
{
    const NAME = 'Faker\\html';

    public function run(...$arguments): BaseLiteral
    {
        $maxDepth = 2;
        if (isset($arguments[0])) {
            $maxDepth = $arguments[0]->toNumber()->getValue();
        }
        $maxWidth = 3;
        if (isset($arguments[1])) {
            $maxWidth = $arguments[0]->toNumber()->getValue();
        }
        return new StringLiteral($this->faker->randomHtml($maxDepth, $maxWidth));
    }

}