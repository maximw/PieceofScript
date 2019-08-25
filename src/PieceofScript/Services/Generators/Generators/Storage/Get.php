<?php


namespace PieceofScript\Services\Generators\Generators\Storage;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\Storage\Services\Storage;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NullLiteral;
use PieceofScript\Services\Values\StringLiteral;

class Get extends BaseStorageGenerator
{
    const NAME = 'storage\\get';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 1) {
            throw new ArgumentsCountError(self::NAME, count($this->arguments), 1);
        }
        if (!$this->arguments[0] instanceof StringLiteral) {
            throw new ArgumentTypeError(self::NAME, $this->arguments[0]::TYPE_NAME, StringLiteral::TYPE_NAME);
        }

        $key = $this->arguments[0]->getValue();

        $defaultValue = new NullLiteral();
        if (isset($this->arguments[1])) {
            $defaultValue = $this->arguments[1];
        }

        if (!$this->storage instanceof Storage) {
            return $defaultValue;
        }

        $result = $this->storage->get($key);

        if (!$result instanceof BaseLiteral) {
            $result = $defaultValue;
            if (!isset($this->arguments[2]) || $this->arguments[2]->toBool() === true) {
                $this->storage->set($key, $defaultValue);
            }
        }

        return $result;
    }

}