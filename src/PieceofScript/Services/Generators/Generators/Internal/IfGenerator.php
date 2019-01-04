<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\InternalGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NullLiteral;

class IfGenerator extends InternalGenerator
{
    const NAME = 'if';

    public function run(): BaseLiteral
    {
        if (!$this->hasNextArgument()) {
            throw new ArgumentsCountError(self::NAME, 0, 2);
        }

        $condition = $this->getNextArgument();

        if ($condition->toBool()->getValue()) {
            if (!$this->hasNextArgument()) {
                throw new ArgumentsCountError(self::NAME, 0, 2);
            }
            $result = $this->getNextArgument();
        } else {
            if (!$this->hasNextArgument()) {
                throw new ArgumentsCountError(self::NAME, 0, 2);
            }
            $this->skipNextArgument();
            if ($this->hasNextArgument()) {
                $result = $this->getNextArgument();
            } else {
                $result = new NullLiteral();
            }
        }
        return $result;
    }

}