<?php

namespace PieceofScript\Services\Generators\Generators;

use PieceofScript\Services\Utils\Utils;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;

/**
 * Generate values from YAML definition
 *
 * @package PieceofScript\Services\Generators\Generators
 */
class YamlGenerator extends BaseGenerator
{
    /**
     * Generator value
     */
    protected $body;

    /**
     * Fields to replace
     */
    protected $replace;

    /**
     * Fields to remove
     */
    protected $remove;

    /**
     * @param BaseLiteral[] ...$arguments
     * @return BaseLiteral
     */
    public function run(...$arguments): BaseLiteral
    {
        $body = $this->parser->evaluate($this->body, $this->contextStack);
        if (null !== $this->replace) {
            $replace = $this->parser->evaluate($this->replace, $this->contextStack);
            $this->replaceFields($body, $replace);
        }
        if (null !== $this->remove) {
            $remove = $this->parser->evaluate($this->remove, $this->contextStack);
            $this->removeFields($body, $remove);
        }

        return Utils::wrapValueContainer($body);
    }

    /**
     * Replace fields in $to
     *
     * @param BaseLiteral $to
     * @param BaseLiteral $from
     */
    protected function replaceFields(BaseLiteral &$to, BaseLiteral $from)
    {
        if (! ($from instanceof ArrayLiteral && $to instanceof ArrayLiteral)) {
            return;
        }
        foreach ($from as $key => $value) {
            if ($value instanceof ArrayLiteral && $to->value[$key] instanceof ArrayLiteral) {
                $this->replaceFields($to->value[$key], $from[$key]);
            } else {
                $to->value[$key] = $value;
            }
        }
    }

    /**
     * Remove fields in $to
     *
     * @param BaseLiteral $to
     * @param BaseLiteral $from
     */
    protected function removeFields(BaseLiteral &$to, BaseLiteral $from)
    {
        if (! ($from instanceof ArrayLiteral && $to instanceof ArrayLiteral)) {
            return;
        }
        foreach ($from as $key => $value) {
            if ($value instanceof ArrayLiteral && $to->value[$key] instanceof ArrayLiteral) {
                $this->removeFields($to->value[$key], $from[$key]);
            } else {
                unset($to->value[$key]);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     * @return YamlGenerator
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReplace()
    {
        return $this->replace;
    }

    /**
     * @param mixed $replace
     * @return YamlGenerator
     */
    public function setReplace($replace)
    {
        $this->replace = $replace;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRemove()
    {
        return $this->remove;
    }

    /**
     * @param mixed $remove
     * @return YamlGenerator
     */
    public function setRemove($remove)
    {
        $this->remove = $remove;
        return $this;
    }

}