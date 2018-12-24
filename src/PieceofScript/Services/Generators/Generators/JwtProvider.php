<?php

namespace PieceofScript\Services\Generators\Generators;


use PieceofScript\Services\Generators\Generators\Faker\Decode;
use PieceofScript\Services\Generators\IGeneratorProvider;

class JwtProvider implements IGeneratorProvider
{

    public function getGenerators(): array
    {
        return [
            new Decode(),

        ];
    }

}