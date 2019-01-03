<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Generators\Generators\InternalGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NullLiteral;

class Choice extends InternalGenerator
{
    const NAME = 'choice';

    public function run(): BaseLiteral
    {
        while ($this->hasNextArgument()) {
            if ($this->getNextArgument()->toBool()->getValue()) {
                if ($this->hasNextArgument()) {
                    return $this->getNextArgument();
                } else {
                    return new NullLiteral();
                }
            }
        }
        return new NullLiteral();
    }

}