<?php


namespace PieceofScript\Services\Utils;


interface IStack
{
    public function push($value);

    public function head();

    public function pop();

    public function reset();

    public function isEmpty(): bool;
}