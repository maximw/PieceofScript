<?php

namespace PieceofScript\Services\Generators\Generators;

use PieceofScript\Services\Contexts\AbstractContext;
use PieceofScript\Services\Contexts\GeneratorContext;
use PieceofScript\Services\Errors\InternalFunctionsErrors\ArgumentsCountError;
use PieceofScript\Services\Out\Out;
use PieceofScript\Services\Utils\Utils;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\VariableName;

/**
 * Generate values from YAML definition
 *
 * @package PieceofScript\Services\Generators\Generators
 */
class YamlGenerator extends BaseGenerator
{

    /**
     * @var VariableName[]
     */
    protected $parameters = [];

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

    public function __construct($name, $parameters = [], $fileName = null)
    {
        parent::__construct($name, $fileName);
        $this->setParameters($parameters);
    }

    public function init()
    {
        parent::init();

        $arguments = [];
        while ($this->hasNextArgument()) {
            $arguments[] = $this->getNextArgument();
        }

        $parameters = $this->getParameters();

        if (count($parameters) > count($arguments)) {
            throw new ArgumentsCountError($this->getName(), count($arguments), count($parameters));
        }
        if (count($parameters) < count($arguments)) {
            Out::printWarning('generator ' . $this->getName() . ' requires ' . count($parameters) . ' arguments, but ' . count($arguments) . ' given', $this->contextStack);
        }

        $context = new GeneratorContext(
            $this->getName(),
            $this->getFileName()
        );
        $this->contextStack->push($context);

        for ($i = 0; $i < count($parameters); $i++) {
            $context->setVariable($parameters[$i], $arguments[$i], AbstractContext::ASSIGNMENT_MODE_VARIABLE);
        }

        $this->setContext($context);
    }

    public function run(): BaseLiteral
    {
        $body = $this->evaluator->evaluate($this->body, $this->context);
        if (null !== $this->replace) {
            $replace = $this->evaluator->evaluate($this->replace, $this->context);
            $this->replaceFields($body, $replace);
        }
        if (null !== $this->remove) {
            $remove = $this->evaluator->evaluate($this->remove, $this->context);
            $this->removeFields($body, $remove);
        }

        return Utils::wrapValueContainer($body);
    }

    public function final()
    {
        parent::final();
        $this->contextStack->pop();
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
            } elseif ($from[$key]->toBool()->getValue()) {
                unset($to->value[$key]);
            }
        }
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return YamlGenerator
     */
    public function setParameters(array $parameters): YamlGenerator
    {
        $this->parameters = $parameters;
        return $this;
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
    public function setBody($body): YamlGenerator
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
    public function setReplace($replace): YamlGenerator
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
    public function setRemove($remove): YamlGenerator
    {
        $this->remove = $remove;
        return $this;
    }

}