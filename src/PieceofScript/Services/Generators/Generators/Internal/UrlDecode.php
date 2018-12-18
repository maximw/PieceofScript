<?php


namespace PieceofScript\Services\Generators\Generators\Internal;


use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Generators\Generators\InternalGenerator;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\StringLiteral;

/**
 * URL-decodes string
 */
class UrlDecode extends InternalGenerator
{
    const NAME = 'urlDecode';

    public function run(...$params): BaseLiteral
    {
        if (count($params) < 1) {
            throw new ArgumentsCountError(self::NAME, 0, 1);
        }

        /** @var BaseLiteral $params[0] */
        return new StringLiteral(rawurldecode($params[0]->toString()));
    }

}