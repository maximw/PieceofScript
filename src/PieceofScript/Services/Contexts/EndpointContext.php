<?php

namespace PieceofScript\Services\Contexts;


use PieceofScript\Services\Tester;

class EndpointContext extends AbstractContext
{
    const DISALLOWED_OPERATORS = [
        Tester::OPERATOR_TESTCASE,
    ];
}