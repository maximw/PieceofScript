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

    public function run(): BaseLiteral
    {
        $country = null;
        if (isset($this->arguments[0])) {
            if (!$this->arguments[0] instanceof StringLiteral && !$this->arguments[0] instanceof NullLiteral) {
                throw new ArgumentTypeError(self::NAME, 0, $this->arguments[0]::TYPE_NAME, StringLiteral::TYPE_NAME);
            }
            if ($this->arguments[0] instanceof StringLiteral) {
                $country = $this->arguments[0]->getValue();
            }
        }

        return new StringLiteral($this->faker->iban($country));
    }

}