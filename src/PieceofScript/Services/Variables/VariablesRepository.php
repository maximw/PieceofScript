<?php


namespace PieceofScript\Services\Variables;

use function DeepCopy\deep_copy;
use PieceofScript\Services\Contexts\AbstractContext;
use PieceofScript\Services\Errors\Parser\ParserError;
use PieceofScript\Services\Utils\Utils;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\Hierarchy\IKeyValue;
use PieceofScript\Services\Values\NullLiteral;
use PieceofScript\Services\Values\StringLiteral;
use PieceofScript\Services\Values\VariableName;
use PieceofScript\Services\Values\VariableReference;

class VariablesRepository
{
    public $variables = [];

    public $assignmentModes = [];

    protected $response;

    /**
     * Get variable value
     *
     * @param VariableName $varName
     * @return string
     * @throws \Exception
     */
    public function get(VariableName $varName): BaseLiteral
    {
        if (!$this->existsWithoutPath($varName)) {
            throw new \Exception('Variable "' . $varName->name . '" not found ');
        }

        if ($this->variables[$varName->name] instanceof VariableReference) {
            return ($this->variables[$varName->name]->get)($varName->path);
        } else {
            return $this->getVal($varName->path, $this->variables[$varName->name]);
        }
    }

    /**
     * Get value if variable fields or indexes were given
     *
     * @param array $path
     * @param mixed $value
     * @return string
     * @throws \Exception
     */
    protected function getVal(array $path, BaseLiteral $value): BaseLiteral
    {
        if (empty($path)) {
            return $value;
        }

        $key = array_shift($path);
        $key = $this->keyToScalar($key);

        if ($value instanceof ArrayLiteral) {
            if (!$value->offsetExists($key)) {
                throw new \Exception('Cannot extract value');
            }
            return $this->getVal($path, $value[$key]);
        } elseif ($value instanceof StringLiteral && (string)(int) $key === (string) $key) {
            $key = (int) $key;
            if ($key < 0 || $key >= mb_strlen($value->getValue(), 'UTF-8')) {
                throw new \Exception('Cannot extract value');
            }
            return new StringLiteral(mb_substr($value->getValue(), $key, 1));
        }

        throw new \Exception('Cannot extract value');
    }

    /**
     * Set variable value
     *
     * @param VariableName $varName
     * @param BaseLiteral $value
     * @param string $assignmentMode
     */
    public function set(VariableName $varName, BaseLiteral $value, string $assignmentMode = AbstractContext::ASSIGNMENT_MODE_VARIABLE)
    {
        if (AbstractContext::ASSIGNMENT_MODE_OFF === $assignmentMode) {
            throw new ParserError('Cannot assign value to variable "'. (string) $varName .'". Did you mean == instead of = ?');
        }

        $currentValue = new NullLiteral();
        if ($this->existsWithoutPath($varName)) {
            $currentValue = $this->variables[$varName->name];
        }

        $value = Utils::wrapValueContainer($value);

        if ($currentValue instanceof VariableReference) {
            ($currentValue->set)($varName->path, $value);
        } else {
            $this->setVal($varName->path, $currentValue, $value);

            if (!isset($this->assignmentModes[$varName->name]) || $this->assignmentModes[$varName->name] === AbstractContext::ASSIGNMENT_MODE_VARIABLE) {
                $this->variables[$varName->name] = $currentValue;
                $this->assignmentModes[$varName->name] = $assignmentMode;
            } else {
                throw new ParserError('Cannot change constant value "' . (string) $varName . '"');
            }
        }
    }

    /**
     * Change $varValue deep in $path with $value
     *
     * @param array $path
     * @param BaseLiteral $currentValue
     * @param BaseLiteral $value
     * @return mixed
     * @throws \Exception
     */
    protected function setVal(array $path, &$currentValue, $value)
    {
        if (empty($path)) {
            return $currentValue = deep_copy($value);
        }

        $key = array_shift($path);
        $key = $this->keyToScalar($key);

        if ($currentValue instanceof ArrayLiteral) {
            if (!$currentValue->offsetExists($key)) {
                $currentValue[$key] = null;
            }
            $this->setVal($path, $currentValue->value[$key], $value);
        } else {
            $currentValue = new ArrayLiteral();
            $this->setVal($path, $currentValue->value[$key], $value);
        }
    }

    /**
     * Creates reference to variable or variable's field in current context
     *
     * @param VariableName $varName
     * @return VariableReference
     */
    public function getReference(VariableName $varName): VariableReference
    {
        $get = function ($path) use ($varName)  {
            $tmpVarName = clone $varName;
            $tmpVarName->path = array_merge($varName->path, $path);
            return $this->get($tmpVarName);
        };

        $set = function ($path, $value) use ($varName)  {
            $tmpVarName = clone $varName;
            $tmpVarName->path = array_merge($varName->path, $path);
            $this->set($tmpVarName, $value);
        };

        $exists = function ($path) use ($varName)  {
            $tmpVarName = clone $varName;
            $tmpVarName->path = array_merge($varName->path, $path);
            return $this->exists($tmpVarName);
        };

        return new VariableReference($get, $set, $exists);
    }

    /**
     * Add named reference to current context
     *
     * @param VariableName $varName
     * @param VariableReference $reference
     * @throws \Exception
     */
    public function setReference(VariableName $varName, VariableReference $reference)
    {
        if (!$varName->isSimple()) {
            throw new \Exception('Only without path');
        }
        $this->variables[$varName->name] = $reference;
    }


    /**
     * Check if variable and all given fields and indexes exists
     *
     * @param VariableName $varName
     * @param bool $checkPath
     * @return bool
     */
    public function exists(VariableName $varName, bool $checkPath = true): bool
    {
        if (!$this->existsWithoutPath($varName)) {
            return false;
        } elseif (!$checkPath) {
            return true;
        }

        if ($this->variables[$varName->name] instanceof VariableReference) {
            return ($this->variables[$varName->name]->get)($varName->path, $checkPath);
        } else {
            return $this->existsPath($varName->path, $this->variables[$varName->name]);
        }

        return false;
    }

    /**
     * Check if variable without fields nor indexes exists
     *
     * @param VariableName $varName
     * @return bool
     */
    public function existsWithoutPath($varName): bool
    {
        return \array_key_exists($varName->name, $this->variables);
    }

    /**
     * Checks if all fields and indexes exists in variable value
     *
     * @param array $path
     * @param mixed $value
     * @return bool
     */
    protected function existsPath(array $path, BaseLiteral $value): bool
    {
        if (empty($path)) {
            return true;
        }

        $key = array_shift($path);
        $key = $this->keyToScalar($key);

        if ($value instanceof ArrayLiteral) {
            if (!$value->offsetExists($key)) {
                return false;
            }
            return $this->existsPath($path, $value[$key]);
        } elseif ($value instanceof StringLiteral && (string)(int) $key === (string) $key) {
            $key = (int) $key;
            if ($key >= 0 && $key < mb_strlen($value->getValue(), 'UTF-8')) {
                return true;
            }
        }
        return false;
    }

    protected function keyToScalar($key)
    {
        if ($key instanceof IKeyValue) {
            $key = $key->toKey();
        } elseif (!is_scalar($key)) {
            throw new \Exception('Array access requires scalar key value');
        }
        return $key;
    }

}