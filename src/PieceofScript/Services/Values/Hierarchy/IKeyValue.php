<?php


namespace PieceofScript\Services\Values\Hierarchy;

/**
 * Implementer literal could be used as key of array
 *
 * Interface IScalarValue
 * @package PieceofScript\Services\Values\Hierarchy
 */
interface IKeyValue
{
    /**
     * @returns string|int
     */
    public function toKey();
}