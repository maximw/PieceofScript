<?php

namespace PieceofScript\Services\Contexts;


use PieceofScript\Services\Tester;

class GlobalContext extends AbstractContext
{
    const DISALLOWED_OPERATORS = [
        Tester::OPERATOR_IMPORT,
    ];
}