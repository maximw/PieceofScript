<?php


namespace PieceofScript\Services\Call;


class BaseCallItem
{

    protected $value;

    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public function isEqual(BaseCallItem $callItem): bool
    {
        if (get_class($this) !== get_class($callItem)) {
            return false;
        }

        return true;
    }

}