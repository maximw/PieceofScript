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

    public function run(...$arguments): BaseLiteral
    {
        if (count($arguments) < 1) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }
        if (!$arguments[0] instanceof StringLiteral) {
            throw new ArgumentTypeError(self::NAME, 0, StringLiteral::TYPE_NAME, $arguments[0]::TYPE_NAME);
        }

        if (!is_dir($arguments[0]) || !is_readable($arguments[0])) {
            throw new GeneratorInternalException(self::NAME . ' argument is not readable directory "' . $arguments[0] . '"');
        }
        if (realpath(Config::get()->getCacheDir()) == realpath($arguments[0])) {
            throw new GeneratorInternalException(self::NAME . ' can not use directory configurated as cache_dir "' . $arguments[0] . '"');
        }
        try {
            $result = new StringLiteral($this->faker->file($arguments[0], Config::get()->getCacheDir()));
        } catch (\InvalidArgumentException $e) {
            throw new GeneratorInternalException(self::NAME . ': ' . $e->getMessage());
        }
        return $result;
    }

}