<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NullLiteral;
use PieceofScript\Services\Values\NumberLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerPassword extends FakerGenerator
{
    const NAME = 'Faker\\password';

    public function run(): BaseLiteral
    {
        $min = 6;
        $max = 20;

        if (isset($this->arguments[0])) {
            if (!$this->arguments[0] instanceof NumberLiteral && !$this->arguments[0] instanceof NullLiteral) {
                throw new ArgumentTypeError(self::NAME, 0, $this->arguments[0]::TYPE_NAME, NumberLiteral::TYPE_NAME);
            }
            if ($this->arguments[0] instanceof NumberLiteral) {
                $min = $this->arguments[0]->getValue();
            }
        }

        if (isset($this->arguments[1])) {
            if (!$this->arguments[1] instanceof NumberLiteral && !$this->arguments[1] instanceof NullLiteral) {
                throw new ArgumentTypeError(self::NAME, 1, $this->arguments[1]::TYPE_NAME, NumberLiteral::TYPE_NAME);
            }
            if ($this->arguments[1] instanceof NumberLiteral) {
                $max = $this->arguments[1]->getValue();
            }
        }

        return new StringLiteral($this->faker->password($min, $max));
    }

}