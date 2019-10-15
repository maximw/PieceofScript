<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\BoolLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\Hierarchy\IScalarValue;

/**
 * Check if arguments Identical
 */
class Identical extends ParametrizedGenerator
{
    const NAME = 'identical';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 2) {
            throw new ArgumentsCountError(self::NAME, count($this->arguments), 2);
        }

        return new BoolLiteral($this->isSimilar($this->arguments[0], $this->arguments[1]));
    }

    protected function isSimilar(BaseLiteral $param1, BaseLiteral $param2): bool
    {
        if ($param1 instanceof IScalarValue || $param2 instanceof IScalarValue) {
            return $param1::TYPE_NAME === $param2::TYPE_NAME;
        }

        /**
         * @var ArrayLiteral $param1
         * @var ArrayLiteral $param2
         * @var BaseLiteral $value
         */
        foreach ($param2 as $key => $value) {
            if (!array_key_exists($key, $param1->getValue())) {
                return false;
            }
            if (($param1[$key])::TYPE_NAME !== ($param2[$key])::TYPE_NAME) {
                return false;
            }
            if ($param1[$key] instanceof ArrayLiteral) {
                if ((string) (int) $key !== (string) $key) {
                    return $this->isSimilar($param1[$key], $param2[$key]);
                }
            }
        }

        foreach ($param1 as $key => $value) {
            if (!array_key_exists($key, $param2->getValue())) {
                return false;
            }
            if (($param1[$key])::TYPE_NAME !== ($param2[$key])::TYPE_NAME) {
                return false;
            }
            if ($param1[$key] instanceof ArrayLiteral) {
                if ((string) (int) $key !== (string) $key) {
                    return $this->isSimilar($param1[$key], $param2[$key]);
                }
            }
        }
        return true;
    }

}