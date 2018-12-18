<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerPersonTitle extends FakerGenerator
{
    const NAME = 'Faker\\personTitle';

    public function run(...$arguments): BaseLiteral
    {
        $gender = null;
        if (isset($arguments[0])) {
            $gender = $arguments[0]->toString()->getValue();
            if (strtolower($gender) !== 'male' && strtolower($gender) !== 'female') {
                $gender = null;
            }
        }

        return new StringLiteral($this->faker->title($gender));
    }

}