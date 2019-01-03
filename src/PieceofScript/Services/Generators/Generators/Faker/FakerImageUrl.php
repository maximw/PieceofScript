<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerImageUrl extends FakerGenerator
{
    const NAME = 'Faker\\imageUrl';

    public function run(): BaseLiteral
    {
        $width = 640;
        if (isset($this->arguments[0])) {
            $width = $this->arguments[0]->toNumber()->getValue();
        }

        $height = 480;
        if (isset($this->arguments[1])) {
            $height = $this->arguments[1]->toNumber()->getValue();
        }

        $category = null;
        if (isset($this->arguments[2])) {
            $category = $this->arguments[2]->toString()->getValue();
        }

        $fullPath = null;
        if (isset($this->arguments[3])) {
            $fullPath = $this->arguments[2]->toBool()->getValue();
        }

        $randomize = null;
        if (isset($this->arguments[4])) {
            $randomize = $this->arguments[4]->toBool()->getValue();
        }

        $word = null;
        if (isset($this->arguments[5])) {
            $word = $this->arguments[5]->toString()->getValue();
        }

        return new StringLiteral($this->faker->imageUrl($width, $height, $category, $fullPath, $randomize, $word));
    }

}