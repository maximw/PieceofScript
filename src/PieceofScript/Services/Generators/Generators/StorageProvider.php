<?php

namespace PieceofScript\Services\Generators\Generators;


use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Generators\Generators\Storage\Get;
use PieceofScript\Services\Generators\Generators\Storage\Keys;
use PieceofScript\Services\Generators\Generators\Storage\Services\Storage;
use PieceofScript\Services\Generators\Generators\Storage\Set;
use PieceofScript\Services\Generators\IGeneratorProvider;

class StorageProvider implements IGeneratorProvider
{
    protected $storage;

    public function __construct()
    {
        if (null !== Config::get()->getStorageName()) {
            $this->storage = new Storage(Config::get()->getStorageName());
        }
    }

    public function getGenerators(): array
    {
        return [
            new Get($this->storage),
            new Keys($this->storage),
            new Set($this->storage),
        ];
    }

}