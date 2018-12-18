<?php


namespace PieceofScript\Services\Values;


use PieceofScript\Services\Values\Hierarchy\Operand;

class VariableName extends Operand
{
    const MODE_VALUE = '$';
    const MODE_TYPE = '@';

    /** @var string */
    public $name;

    /** @var array */
    public $path = [];

    /**
     * Return $Value or @Type of variable
     * @var bool
     */
    public $mode = '$';

    public function __construct($name)
    {
        $this->name = substr($name, 1);
        $this->mode = substr($name, 0, 1);
    }

    public function addPath($pathItem): self
    {
        array_push($this->path, $pathItem);
        return $this;
    }

    public function isSimple(): bool
    {
        return \count($this->path) === 0;
    }

    public function __toString()
    {
        return static::MODE_VALUE . $this->name . ($this->isSimple() ? '' : ('.' . implode('.', $this->path)));
    }

}