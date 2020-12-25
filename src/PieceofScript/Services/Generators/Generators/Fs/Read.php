<?php


namespace PieceofScript\Services\Generators\Generators\Fs;


use PieceofScript\Services\Errors\GeneratorInternalException;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NumberLiteral;
use PieceofScript\Services\Values\StringLiteral;

class Read extends ParametrizedGenerator
{
    const NAME = 'fs\\read';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 1) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }

        if (!$this->arguments[0] instanceof StringLiteral) {
            throw new ArgumentTypeError(self::NAME, 0, $this->arguments[0]::TYPE_NAME, StringLiteral::TYPE_NAME);
        }

        if (!empty($this->arguments[1]) && ! $this->arguments[1] instanceof NumberLiteral) {
            throw new ArgumentTypeError(self::NAME, 1, $this->arguments[1]::TYPE_NAME, NumberLiteral::TYPE_NAME);
        }

        if (!empty($this->arguments[2]) && ! $this->arguments[2] instanceof NumberLiteral) {
            throw new ArgumentTypeError(self::NAME, 2, $this->arguments[2]::TYPE_NAME, NumberLiteral::TYPE_NAME);
        }

        $fp = fopen($this->arguments[0]->getValue(), 'r');
        if ($fp === false) {
            throw new GeneratorInternalException(self::NAME . ': can not read file ' . $this->arguments[0]->getValue());
        }

        $offset = 0;
        if ($this->arguments[1] instanceof NumberLiteral) {
            $offset = (int) $this->arguments[1]->getValue();
        }

        if ($this->arguments[2] instanceof NumberLiteral) {
            $length = (int) $this->arguments[2]->getValue();
        } else {
            $length = filesize($this->arguments[0]->getValue());
            if ($length === false) {
                throw new GeneratorInternalException(self::NAME . ': can not get file size ' . $this->arguments[0]->getValue());
            }
        }

        $flag = SEEK_SET;
        if ($offset < 0) {
            $flag = SEEK_END;
        }
        $error = fseek($fp, $offset, $flag);
        if ($error !== 0) {
            throw new GeneratorInternalException(self::NAME . ': can not seek in file to ' . $offset);
        }

        $result = fread($fp, $length);
        if ($result === false) {
            throw new GeneratorInternalException(self::NAME . ': can not read ' . $length . ' bytes from file ' . $this->arguments[0]->getValue());
        }

        fclose($fp);
        return new StringLiteral($result);
    }

}