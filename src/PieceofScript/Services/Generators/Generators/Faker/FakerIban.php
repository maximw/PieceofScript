<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NullLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerIban extends FakerGenerator
{
    const NAME = 'Faker\\BankCardExpiration';

    public function run(...$arguments): BaseLiteral
    {
        $country = null;
        if (isset($arguments[0])) {
            if (!$arguments[0] instanceof StringLiteral && !$arguments[0] instanceof NullLiteral) {
                throw new ArgumentTypeError(self::NAME, 0, $arguments[0]::TYPE_NAME, StringLiteral::TYPE_NAME);
            }
            if ($arguments[0] instanceof StringLiteral) {
                $country = $arguments[0]->getValue();
            }
        }

        return new StringLiteral($this->faker->iban($country));
    }

}