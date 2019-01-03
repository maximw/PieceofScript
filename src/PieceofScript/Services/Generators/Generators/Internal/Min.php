<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

class Min extends ParametrizedGenerator
{
    const NAME = 'min';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 1) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }

        /** @var BaseLiteral $min */
        $min = $this->arguments[0];
        foreach ($this->arguments as $param) {
            if ($min->oGreater($param)->getValue()) {
                $min = $param;
            }
        }

        return $min;
    }

}