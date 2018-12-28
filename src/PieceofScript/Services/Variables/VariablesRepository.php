<?php


namespace PieceofScript\Services\Variables;

use function DeepCopy\deep_copy;
use PieceofScript\Services\Contexts\AbstractContext;
use PieceofScript\Services\Errors\Parser\ParserError;
use PieceofScript\Services\Errors\Parser\VariableError;
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
     * @throws VariableError
     */
    public function get(VariableName $varName): BaseLiteral
    {
        if (!$this->existsWithoutPath($varName)) {
            throw new VariableError($varName,'variable does not exist.');
        }

        if ($this->variables[$varName->name] instanceof VariableReference) {
            return ($this->variables[$varName->name]->get)($varName->path);
        } else {
            return $this->getVal($varName->path, $this->variables[$varName->name], $varName);
        }
    }

    /**
     * Get value if variable fields or indexes were given
     *
     * @param array $path
     * @param mixed $value
     * @param VariableName $variableName
     * @return BaseLiteral
     * @throws VariableError
     */
    protected function getVal(array $path, BaseLiteral $value, VariableName $variableName): BaseLiteral
    {
        if (empty($path)) {
            return $value;
        }

        $key = array_shift($path);
        $key = $this->keyToScalar($key, $variableName);

        if ($value instanceof ArrayLiteral) {
            if (!$value->offsetExists($key)) {
                throw new VariableError($variableName, 'array key "' . $key . '" does not exist.');
            }
            return $this->getVal($path, $value[$key], $variableName);
        } elseif ($value instanceof StringLiteral && (string)(int) $key === (string) $key) {
            $key = (int) $key;
            if ($key < 0 || $key >= mb_strlen($value->getValue(), 'UTF-8')) {
                throw new VariableError($variableName, 'string offset "' . $key . '" does not exist.');
            }
            return new StringLiteral(mb_substr($value->getValue(), $key, 1));
        }

        throw new VariableError($variableName, 'trying to get element "' . $key . '" of '. $value::TYPE_NAME);
    }

    /**
     * Set variable value
     *
     * @param VariableName $varName
     * @param BaseLiteral $value
     * @param string $assignmentMode
     * @throws VariableError
     */
    public function set(VariableName $varName, BaseLiteral $value, string $assignmentMode = AbstractContext::ASSIGNMENT_MODE_VARIABLE)
    {
        if (AbstractContext::ASSIGNMENT_MODE_OFF === $assignmentMode) {
            throw new VariableError($varName, 'cannot assign value here. Did you mean == instead of = ?');
        }

        $currentValue = new NullLiteral();
        if ($this->existsWithoutPath($varName)) {
            $currentValue = $this->variables[$varName->name];
        }

        $value = Utils::wrapValueContainer($value);

        if ($currentValue instanceof VariableReference) {
            ($currentValue->set)($varName->path, $value);
        } else {
            $this->setVal($varName->path, $currentValue, $value, $varName);

            if (!isset($this->assignmentModes[$varName->name]) || $this->assignmentModes[$varName->name] === AbstractContext::ASSIGNMENT_MODE_VARIABLE) {
                $this->variables[$varName->name] = $currentValue;
                $this->assignmentModes[$varName->name] = $assignmentMode;
            } else {
                throw new VariableError($varName, 'cannot change constant value.');
            }
        }
    }

    /**
     * Change $varValue deep in $path with $value
     *
     * @param array $path
     * @param BaseLiteral $currentValue
     * @param BaseLiteral $value
     * @param VariableName $variableName
     * @return mixed
     * @throws VariableError
     */
    protected function setVal(array $path, &$currentValue, $value, VariableName $variableName)
    {
        if (empty($path)) {
            return $currentValue = deep_copy($value);
        }

        $key = array_shift($path);
        $key = $this->keyToScalar($key, $variableName);

        if ($currentValue instanceof ArrayLiteral) {
            if (!$currentValue->offsetExists($key)) {
                $currentValue[$key] = null;
            }
            $this->setVal($path, $currentValue->value[$key], $value, $variableName);
        } else {
            $currentValue = new ArrayLiteral();
            $this->setVal($path, $currentValue->value[$key], $value, $variableName);
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
            throw new VariableError($varName, ' cannot make reference. Did you mean just $' . $varName->name);
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
            return $this->existsPath($varName->path, $this->variables[$varName->name], $varName);
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
     * @param VariableName $variableName
     * @return bool
     * @throws VariableError
     */
    protected function existsPath(array $path, BaseLiteral $value, VariableName $variableName): bool
    {
        if (empty($path)) {
            return true;
        }

        $key = array_shift($path);
        $key = $this->keyToScalar($key, $variableName);

        if ($value instanceof ArrayLiteral) {
            if (!$value->offsetExists($key)) {
                return false;
            }
            return $this->existsPath($path, $value[$key], $variableName);
        } elseif ($value instanceof StringLiteral && (string)(int) $key === (string) $key) {
            $key = (int) $key;
            if ($key >= 0 && $key < mb_strlen($value->getValue(), 'UTF-8')) {
                return true;
            }
        }
        return false;
    }

    protected function keyToScalar($key, VariableName $variableName)
    {
        if ($key instanceof IKeyValue) {
            $key = $key->toKey();
        } elseif (!is_scalar($key)) {
            throw new VariableError($variableName, 'array access requires scalar key');
        }
        return $key;
    }

}