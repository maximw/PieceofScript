<?php


namespace PieceofScript\Services\Generators\Generators\Faker;


use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Errors\GeneratorInternalException;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\FakerGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class FakerFile extends FakerGenerator
{
    const NAME = 'Faker\\file';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) < 1) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }
        if (!$this->arguments[0] instanceof StringLiteral) {
            throw new ArgumentTypeError(self::NAME, 0, $this->arguments[0]::TYPE_NAME, StringLiteral::TYPE_NAME);
        }

        if (!is_dir($this->arguments[0]) || !is_readable($this->arguments[0])) {
            throw new GeneratorInternalException(self::NAME . ' argument is not readable directory "' . $this->arguments[0] . '"');
        }
        if (realpath(Config::get()->getCacheDir()) == realpath($this->arguments[0])) {
            throw new GeneratorInternalException(self::NAME . ' can not use directory configured as cache_dir "' . $this->arguments[0] . '"');
        }
        try {
            $result = new StringLiteral($this->faker->file($this->arguments[0], Config::get()->getCacheDir()));
        } catch (\InvalidArgumentException $e) {
            throw new GeneratorInternalException(self::NAME . ': ' . $e->getMessage());
        }
        return $result;
    }

}