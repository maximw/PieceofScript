<?php


namespace PieceofScript\Services;


use PieceofScript\Services\Errors\FileNotFoundError;

class FilesRepository
{
    protected $files = [];

    public function __construct()
    {
    }

    public function read(string $fileName)
    {
        $realFileName = realpath($fileName);

        if (!$realFileName || !file_exists($realFileName) || !is_readable($realFileName)) {
            throw new FileNotFoundError('Cannot read file ' . $fileName);
        }

        if (\array_key_exists($realFileName, $this->files)) {
            return $this->files[$realFileName];
        }
        chdir(dirname($realFileName));

        $this->files[$realFileName] = file($realFileName);
        return $this->files[$realFileName];
    }

}