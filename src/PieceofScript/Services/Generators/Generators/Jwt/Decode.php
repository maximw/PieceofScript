<?php


namespace PieceofScript\Services\Generators\Generators\Jwt;


use Lcobucci\JWT\Parser;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\InternalGenerator;
use PieceofScript\Services\Utils\Utils;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class Decode extends InternalGenerator
{
    const NAME = 'jwt\\decode';

    public function run(...$arguments): BaseLiteral
    {
        if (count($arguments) !== 1) {
            throw new ArgumentsCountError(self::NAME, count($arguments), 1);
        }
        if (!$arguments[0] instanceof StringLiteral) {
            throw new ArgumentTypeError(self::NAME, $arguments[0]::TYPE_NAME, StringLiteral::TYPE_NAME);
        }

        $token = (new Parser())->parse((string) $arguments[0]->getValue());
        $result = [
            'headers' => $token->getHeaders(),
            'claims' => $token->getClaims()
        ];

        return Utils::wrapValueContainer($result);
    }

}