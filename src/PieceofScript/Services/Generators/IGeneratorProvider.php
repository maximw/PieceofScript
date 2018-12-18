<?php


namespace PieceofScript\Services\Generators;


interface IGeneratorProvider
{

    /** @return IGenerator[] */
    public function getGenerators(): array;

}