<?php

namespace PieceofScript\Services\Generators\Generators;


use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Generators\Generators\LocalStorage\Get;
use PieceofScript\Services\Generators\Generators\LocalStorage\Keys;
use PieceofScript\Services\Generators\Generators\LocalStorage\Services\LocalStorage;
use PieceofScript\Services\Generators\Generators\LocalStorage\Set;
use PieceofScript\Services\Generators\IGeneratorProvider;

class LocalStorageProvider implements IGeneratorProvider
{
    protected $localStorage;

    public function __construct()
    {
        if (null !== Config::get()->getLocalStorageName()) {
            $this->localStorage = new LocalStorage(Config::get()->getLocalStorageName());
        }
    }

    public function getGenerators(): array
    {
        return [
            new Get($this->localStorage),
            new Keys($this->localStorage),
            new Set($this->localStorage),
        ];
    }

}