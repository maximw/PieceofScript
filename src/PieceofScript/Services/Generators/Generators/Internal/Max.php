<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

class Max extends ParametrizedGenerator
{
    const NAME = 'max';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 1) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }

        /** @var BaseLiteral $max */
        $max = $this->arguments[0];
        foreach ($this->arguments as $param) {
            if ($max->oLower($param)->getValue()) {
                $max = $param;
            }
        }

        return $max;
    }

}