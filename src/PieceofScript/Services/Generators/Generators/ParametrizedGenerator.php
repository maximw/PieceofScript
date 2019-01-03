<?php


namespace PieceofScript\Services\Generators\Generators;


use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

abstract class ParametrizedGenerator extends InternalGenerator
{
    /**
     * Given arguments
     *
     * @var BaseLiteral[]
     */
    protected $arguments = [];

    public function init()
    {
        parent::init();
        while ($this->hasNextArgument()) {
            $this->arguments[] = $this->getNextArgument();
        }
    }

    public function final()
    {
        parent::final();
        $this->arguments = [];
    }
}