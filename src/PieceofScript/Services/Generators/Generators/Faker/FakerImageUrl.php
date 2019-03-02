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
            $width = (int) $this->arguments[0]->toNumber()->getValue();
        }

        $height = 480;
        if (isset($this->arguments[1])) {
            $height = (int) $this->arguments[1]->toNumber()->getValue();
        }

        $category = null;
        if (isset($this->arguments[2])) {
            $category = $this->arguments[2]->toString()->getValue();
        }

        $randomize = null;
        if (isset($this->arguments[3])) {
            $randomize = $this->arguments[3]->toBool()->getValue();
        }

        $word = null;
        if (isset($this->arguments[4])) {
            $word = $this->arguments[4]->toString()->getValue();
        }

        $monochrome = false;
        if (isset($this->arguments[5])) {
            $monochrome = $this->arguments[5]->toBool()->getValue();
        }

        return new StringLiteral($this->faker->imageUrl($width, $height, $category, $randomize, $word, $monochrome));
    }

}