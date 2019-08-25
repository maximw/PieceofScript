<?php


namespace PieceofScript\Services\Generators\Generators\Storage;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\Storage\Services\Storage;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class Set extends BaseStorageGenerator
{
    const NAME = 'storage\\set';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 2) {
            throw new ArgumentsCountError(self::NAME, count($this->arguments), 2);
        }
        if (!$this->arguments[0] instanceof StringLiteral) {
            throw new ArgumentTypeError(self::NAME, $this->arguments[0]::TYPE_NAME, StringLiteral::TYPE_NAME);
        }

        if ($this->storage instanceof Storage) {
            $this->storage->set($this->arguments[0]->getValue(), $this->arguments[1]);
        }

        return $this->arguments[1];
    }

}