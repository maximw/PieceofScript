<?php


namespace PieceofScript\Services\Values;

use PieceofScript\Services\Values\Hierarchy\Operand;

class VariableReference extends Operand
{
    public $get;

    public $set;

    public $exists;

    public function __construct($get, $set, $exists)
    {
        $this->get = $get;
        $this->set = $set;
        $this->exists = $exists;
    }

}