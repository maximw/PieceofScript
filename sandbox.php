<?php

class VariableReference
{
    public $get;

    public $set;

    public $exists;

    public function __construct($get, $set, $exists)
    {
        $this->get = $get;
        $this->set = $set;
        $this->exists = $exists;
    }
}

class VariableName
{
    /** @var string */
    public $name;

    /** @var array */
    public $path = [];

    /** @var string */
    public $originalName;

    public function isSimple(): bool
    {
        return \count($this->path) === 0;
    }
}

class VariablesRepository
{
    protected $variables = [];

    protected $response;

    public function __construct()
    {
    }

    /**
     * Get variable value
     *
     * @param string|VariableName $varName
     * @return string
     * @throws \Exception
     */
    public function get($varName)
    {
        $varName = $this->normalizeVarName($varName);

        if (!$this->existsWithoutPath($varName)) {
            throw new \Exception('Variable "' . $varName->originalName . '" not found ');
        }

        $value = $this->variables[$varName->name];
        if ($value instanceof VariableReference) {
            //$get = $value->get;
            return ($value->get)($varName->path);
        } else {
            return $this->getVal($varName->path, $this->variables[$varName->name]);
        }
    }

    /**
     * Get value if variable fields or indexes were given
     *
     * @param array $varItems
     * @param mixed $value
     * @return string
     * @throws \Exception
     */
    protected function getVal(array $varItems, $value)
    {
        if (empty($varItems)) {
            return $value;
        }

        $item = array_shift($varItems);

        if (is_array($value)) {
            if (!\array_key_exists($item, $value)) {
                throw new \Exception('Cannot extract value');
            }
            return $this->getVal($varItems, $value[$item]);
        } elseif (is_object($value)) {
            if (!property_exists($value, $item)) {
                throw new \Exception('Cannot extract value');
            }
            return $this->getVal($varItems, $value->{$item});
        } if (is_string($value)) {
        $item = (int) $item;
        if ($item < 0 || $item > mb_strlen($value, 'UTF-8')) {
            throw new \Exception('Cannot extract value');
        }
        return mb_substr($value, $item, 1);
    }

        throw new \Exception('Cannot extract value');
    }

    /**
     * Set variable value
     *
     * @param string|VariableName $varName
     * @param mixed $value
     */
    public function set($varName, $value = null)
    {
        $varName = $this->normalizeVarName($varName);

        $currentValue = null;
        if ($this->existsWithoutPath($varName)) {
            $currentValue = $this->variables[$varName->name];
        }

        if ($currentValue instanceof VariableReference) {
            ($currentValue->set)($varName->path, $value);
        } else {
            $this->setVal($varName->path, $currentValue, $value);
            $this->variables[$varName->name] = $currentValue;
        }
    }

    /**
     * Change $varValue deep in $path with $value
     *
     * @param array $path
     * @param mixed $varValue
     * @param mixed $value
     * @return mixed
     * @throws \Exception
     */
    protected function setVal(array $path, &$varValue, $value)
    {
        if (empty($path)) {
            return $varValue = $value;//deep_copy($value);
        }

        $item = array_shift($path);
        if (!ctype_alnum($item)) {
            throw new \Exception('Error of field or index of variable ' . $item);
        }

        if (is_array($varValue)) {
            if (!\array_key_exists($item, $varValue)) {
                $varValue[$item] = null;
            }
            $this->setVal($path, $varValue[$item], $value);
        } elseif (is_object($varValue)) {
            if (!property_exists($varValue, $item)) {
                $varValue->{$item} = null;;
            }
            $this->setVal($path, $varValue->{$item}, $value);
        } else {
            $varValue = [];
            $this->setVal($path, $varValue[$item], $value);
        }
    }

    public function getReference($varName)
    {
        $varName = $this->normalizeVarName($varName);

        $get = function ($path) use ($varName)  {
            $varName->path = array_merge($varName->path, $path);
            return $this->get($varName);
        };

        $set = function ($path, $value) use ($varName)  {
            $varName->path = array_merge($varName->path, $path);
            return $this->set($varName, $value);
        };

        return new VariableReference($get, $set);
    }

    public function setReference($varName, VariableReference $reference)
    {
        $varName = $this->normalizeVarName($varName);
        if (!$varName->isSimple()) {
            throw new Exception('Only without path');
        }
        $this->variables[$varName->name] = $reference;
    }


    /**
     * Check if variable and all given fields and indexes exists
     *
     * @param string|VariableName $varName
     * @return bool
     */
    public function exists($varName): bool
    {
        $varName = $this->normalizeVarName($varName);

        return $this->existsWithoutPath($varName)
            && $this->existsPath($varName->path, $this->variables[$varName->name]);
    }

    /**
     * Check if variable without fields nor indexes exists
     *
     * @param VariableName $varName
     * @return bool
     */
    protected function existsWithoutPath(VariableName $varName): bool
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
    protected function existsPath(array $path, $value): bool
    {
        if (empty($path)) {
            return true;
        }

        $item = array_shift($path);

        if (is_array($value)) {
            if (!\array_key_exists($item, $value)) {
                return false;
            }
            return $this->existsPath($path, $value[$item]);
        } elseif (is_object($value)) {
            if (!property_exists($value, $item)) {
                return false;
            }
            return $this->existsPath($path, $value->{$item});
        } if (is_string($value) && (string)(int) $item === (string) $item) {
        $item = (int) $item;
        if ($item >= 0 && $item < mb_strlen($value, 'UTF-8')) {
            return true;
        }
    }
        return false;
    }

    /**
     * Parse given expression to variable name, fields and indexes, normalize variable name
     *
     * @param string|VariableName $varName
     * @return VariableName
     */
    protected function normalizeVarName($varName): VariableName
    {
        if (is_string($varName)) {
            $name = $varName;
            $varItems = explode('.', $name);
            $varName = new VariableName();
            $varName->name = trim($varItems[0]);
            $varName->path = \array_slice($varItems, 1);
            $varName->originalName = $name;

        }
        return $varName;
    }



}

$repo1 = new VariablesRepository();
$repo2 = new VariablesRepository();
$repo3 = new VariablesRepository();

$repo1->set('foo.a.b', 2);
$ref = $repo1->getReference('foo.a');

$a = $repo1->get('foo');
var_dump($a);

$repo2->setReference('baz', $ref);
$repo2->set('baz.sss.www', 22);

$ref = $repo2->getReference('baz.sss');
$repo3->setReference('tor', $ref);

print_r($repo3->get('tor'));
//$repo3->set('tor.molot', 'bla');

$a = $repo1->get('foo');
print_r($a);

/*
class Value {
    public $val;

    public function __construct($val)
    {
        $this->val = $val;
    }
}

class Repo
{
    protected $vars = [];

    public function get($name) {
        return $this->vars[$name]->val;
    }


    public function set($name, $value) {
        if (array_key_exists($name, $this->vars)) {
            $this->vars[$name]->val = $value;
        } else {
            $this->vars[$name] = new Value($value);
        }
    }


    public function getLink($name){
        return $this->vars[$name];
    }

    public function setLink($name, $val) {
        $this->vars[$name] = $val;
    }

}

$r1 = new Repo;
$r1->set('test', [1, 2]);

$r2 = new Repo;
$r2->setLink('foo', $r1->getLink('test'));

var_dump('---------');
var_dump($r1->get('test'));
var_dump($r2->get('foo'));

var_dump('---------');

$r1->set('test', 6);
var_dump($r1->get('test'));
var_dump($r2->get('foo'));

var_dump('---------');

$r2->set('foo', 7);
var_dump($r1->get('test'));
var_dump($r2->get('foo'));
*/