<?php


namespace PieceofScript\Services\Generators\Generators\LocalStorage;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\LocalStorage\Services\LocalStorage;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class Keys extends BaseLocalStorageGenerator
{
    const NAME = 'ls\\keys';

    public function run(): BaseLiteral
    {
        if (isset($this->arguments[0]) && !$this->arguments[0] instanceof StringLiteral) {
            throw new ArgumentTypeError(self::NAME, $this->arguments[0]::TYPE_NAME, StringLiteral::TYPE_NAME);
        }

        if (!$this->localStorage instanceof LocalStorage) {
            return new ArrayLiteral([]);
        }

        $keys = $this->localStorage->keys();
        if (isset($this->arguments[0])) {
            $regex = $this->arguments[0]->getValue();
            foreach ($keys as $k => $v) {
                if (!preg_match($regex, $v)) {
                    unset($keys[$k]);
                }
            }
        }

        return new ArrayLiteral($keys);
    }

}