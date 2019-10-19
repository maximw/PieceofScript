<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\BoolLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\Hierarchy\IScalarValue;

/**
 * Check if arguments similar
 */
class Similar extends ParametrizedGenerator
{
    const NAME = 'similar';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 2) {
            throw new ArgumentsCountError(self::NAME, count($this->arguments), 2);
        }

        $checkTypes = true;
        if (isset($this->arguments[2])) {
            $checkTypes = $this->arguments[2]->toBool()->getValue();
        }

        return new BoolLiteral($this->isSimilar($this->arguments[0], $this->arguments[1], $checkTypes));
    }

    protected function isSimilar(
        BaseLiteral $variable,
        BaseLiteral $template,
        bool $checkTypes
    ): bool
    {
        if ($variable instanceof IScalarValue && $template instanceof IScalarValue) {
            return !$checkTypes || $variable::TYPE_NAME === $template::TYPE_NAME;;
        }

        /**
         * @var ArrayLiteral $variable
         * @var ArrayLiteral $template
         * @var BaseLiteral $value
         */
        foreach ($template as $key => $value) {
            if (!array_key_exists($key, $variable->getValue())) {
                return false;
            }
            if ($checkTypes && ($variable[$key])::TYPE_NAME !== ($template[$key])::TYPE_NAME) {
                return false;
            }
            if ($variable[$key] instanceof ArrayLiteral) {
                if ((string) (int) $key !== (string) $key) {
                    return $this->isSimilar($variable[$key], $template[$key], $checkTypes);
                }
            }
        }
        return true;
    }

}