<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerPhoneNumber extends FakerGenerator
{
    const NAME = 'Faker\\phoneNumber';

    public function run(...$arguments): BaseLiteral
    {
        $phone = $this->faker->phoneNumber();
        if (isset($arguments[0]) && $arguments[0]->toBoll()->getValue) {
            $phone = $this->faker->e164PhoneNumber();
        }

        return new StringLiteral($phone);
    }

}