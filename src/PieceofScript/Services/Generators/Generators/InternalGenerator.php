<?php


namespace PieceofScript\Services\Generators\Generators;


abstract class InternalGenerator extends BaseGenerator
{
    const NAME = '';

    public function __construct()
    {
        $this->setName(static::NAME);
        $this->setFileName('Internal');
    }
}