<?php


namespace PieceofScript\Services\Generators\Generators\Jwt;


use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentTypeError;
use PieceofScript\Services\Generators\Generators\ParametrizedGenerator;
use PieceofScript\Services\Utils\Utils;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

class Decode extends ParametrizedGenerator
{
    const NAME = 'jwt\\decode';

    public function run(): BaseLiteral
    {
        if (count($this->arguments) !== 1) {
            throw new ArgumentsCountError(self::NAME, count($this->arguments), 1);
        }
        if (!$this->arguments[0] instanceof StringLiteral) {
            throw new ArgumentTypeError(self::NAME, $this->arguments[0]::TYPE_NAME, StringLiteral::TYPE_NAME);
        }

        $token = (new Parser())->parse((string) $this->arguments[0]->getValue());
        $result = [
            'headers' => $token->getHeaders(),
            'claims' => $this->claimsToArray($token),
        ];

        return Utils::wrapValueContainer($result);
    }

    protected function claimsToArray(Token $token)
    {
        $result = [];
        foreach ($token->getClaims() as $claim => $value)
        {
            $result[$claim] = $value->getValue();
        }
        return $result;
    }
}