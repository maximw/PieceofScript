<?php


namespace PieceofScript\Services\Generators\Generators\Storage;


use PieceofScript\Services\Generators\Generators\Storage\Services\Storage;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;

abstract class BaseStorageGenerator extends ParametrizedGenerator
{
    /** @var Storage|null */
    protected $storage;

    public function __construct(string $storage = null)
    {
        parent::__construct();
        $this->storage = $storage;
    }

}