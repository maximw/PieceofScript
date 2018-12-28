<?php


namespace PieceofScript\Services\Utils;


use PieceofScript\Services\Errors\FileNotFoundError;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\BoolLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\Hierarchy\IScalarValue;
use PieceofScript\Services\Values\NullLiteral;
use PieceofScript\Services\Values\NumberLiteral;
use PieceofScript\Services\Values\StringLiteral;

class Utils
{
    public static function fileSearch(string $dir, bool $recursive = true): array
    {
        $realPath = realpath($dir);
        if (is_dir($realPath)) {
            $mask = null;
        } elseif (is_file($realPath)) {
            return [$realPath];
        } else {
            $mask = pathinfo($dir, PATHINFO_BASENAME);
            $dir = pathinfo($dir, PATHINFO_DIRNAME);
            $realPath = realpath($dir);
        }
        if (!is_dir($realPath)) {
            throw new FileNotFoundError('Cannot find directory ' . $dir);
        }
        return self::fileSearchInDir($realPath, $mask, $recursive);
    }

    public static function fileSearchInDir(string $dir, string $mask = null, bool $recursive = true): array
    {
        $dir = self::normalizeDirectoryPath($dir);
        $files = [];
        if (is_dir($dir) && $handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                if (!in_array($file, ['.', '..'])) {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
                        if ($recursive) {
                            $files = array_merge($files, self::fileSearchInDir($dir . DIRECTORY_SEPARATOR . $file, $mask, $recursive));
                        }
                    } else {
                        $filename = $dir . DIRECTORY_SEPARATOR . $file;
                        if (null === $mask || fnmatch($mask, $filename)) {
                            $files[] = $filename;
                        }
                    }
                }
            }
            closedir($handle);
        }
        return $files;
    }

    public static function normalizeDirectoryPath(string $dir)
    {
        return rtrim(realpath($dir), DIRECTORY_SEPARATOR);
    }

    public static function wrapValueContainer($value)
    {
        if ($value instanceof IScalarValue) {
            return $value;
        } elseif (is_numeric($value)) {
            return new NumberLiteral($value);
        } elseif (is_string($value)) {
            return new StringLiteral($value);
        } elseif (is_bool($value)) {
            return new BoolLiteral($value);
        } elseif (is_null($value)) {
            return new NullLiteral();
        } elseif (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = self::wrapValueContainer($item);
            }
            return new ArrayLiteral($value);
        } elseif ($value instanceof ArrayLiteral) {
            foreach ($value as $key => $item) {
                $value[$key] = self::wrapValueContainer($item);
            }
            return $value;
        }
        throw new \Exception('Unhandled value type' . gettype($value));
    }

    public static function unwrapValueContainer(BaseLiteral $value)
    {
        if ($value instanceof IScalarValue) {
            return $value->getValue();
        } elseif ($value instanceof ArrayLiteral) {
            $array = [];
            foreach ($value as $key => $item) {
                $array[$key] = self::unwrapValueContainer($item);
            }
            return $array;
        }
        throw new \Exception('Unhandled value type' . gettype($value));
    }

}