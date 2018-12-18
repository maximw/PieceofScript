<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerImageUrl extends FakerGenerator
{
    const NAME = 'Faker\\imageUrl';

    public function run(...$arguments): BaseLiteral
    {
        $width = 640;
        if (isset($arguments[0])) {
            $width = $arguments[0]->toNumber()->getValue();
        }

        $height = 480;
        if (isset($arguments[1])) {
            $height = $arguments[1]->toNumber()->getValue();
        }

        $category = null;
        if (isset($arguments[2])) {
            $category = $arguments[2]->toString()->getValue();
        }

        $fullPath = null;
        if (isset($arguments[3])) {
            $fullPath = $arguments[2]->toBool()->getValue();
        }

        $randomize = null;
        if (isset($arguments[4])) {
            $randomize = $arguments[4]->toBool()->getValue();
        }

        $word = null;
        if (isset($arguments[5])) {
            $word = $arguments[5]->toString()->getValue();
        }

        return new StringLiteral($this->faker->imageUrl($width, $height, $category, $fullPath, $randomize, $word));
    }

}