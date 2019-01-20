<?php


namespace PieceofScript\Services\Generators\Generators\LocalStorage\Services;


use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Values\ArrayLiteral;
use PieceofScript\Services\Values\BoolLiteral;
use PieceofScript\Services\Values\DateLiteral;
use PieceofScript\Services\Values\Hierarchy\BaseLiteral;
use PieceofScript\Services\Values\NullLiteral;
use PieceofScript\Services\Values\NumberLiteral;
use PieceofScript\Services\Values\StringLiteral;
use Symfony\Component\Yaml\Yaml;

class LocalStorage
{
    protected $file;

    protected $cache = [];

    public function __construct(string $file)
    {
        $this->file = $file;
        $this->load();
    }

    public function set(string $key, $value)
    {
        $this->cache[$key] = $this->encode($value);
        $this->flush();
    }

    public function get(string $key)
    {
        if (array_key_exists($key, $this->cache)) {
            return $this->decode($this->cache[$key]);
        }
        $this->load();
        if (array_key_exists($key, $this->cache)) {
            return $this->decode($this->cache[$key]);
        }
        return false;
    }

    public function keys()
    {
        return array_keys($this->cache);
    }

    protected function load()
    {
        $cache = Yaml::parseFile($this->file);
        foreach ($cache as $key => $value) {
            $this->cache[trim($key, '\'')] = $value;
        }
    }

    protected function flush()
    {
        file_put_contents($this->file, Yaml::dump($this->cache, PHP_INT_MAX));
    }

    protected function decode(array $data)
    {
        if (!isset($data['type']) || !isset($data['data'])) {
            throw new \Exception('Broken structure for in local storage');
        }

        $data['type'] = trim($data['type'], '\'');

        if ($data['type'] === ArrayLiteral::TYPE_NAME) {
            if (!is_array($data['data'])) {
                throw new \Exception('Broken structure for in local storage');
            }
            $result = [];
            foreach ($data['data'] as $key => $value) {
                $result[$key] = $this->decode($value);
            }
            return new ArrayLiteral($result);
        }

        $data['data'] = trim($data['data'], '"\'');

        if ($data['type'] === NullLiteral::TYPE_NAME) {
            return new NullLiteral();
        }

        if ($data['type'] === BoolLiteral::TYPE_NAME) {
            return new BoolLiteral(trim(strtolower($data['data'])) === 'true');
        }

        if ($data['type'] === NumberLiteral::TYPE_NAME) {
            return new NumberLiteral((float) $data['data']);
        }

        if ($data['type'] === StringLiteral::TYPE_NAME) {
            return new StringLiteral($data['data']);
        }

        if ($data['type'] === DateLiteral::TYPE_NAME) {
            return new DateLiteral($data['data']);
        }

        throw new \Exception('Broken structure for in local storage');
    }

    protected function encode(BaseLiteral $data): array
    {
        $result['type'] = $data::TYPE_NAME;

        if ($data instanceof ArrayLiteral) {
            $array = [];
            foreach ($data->getValue() as $key => $value) {
                $array[$key] = $this->encode($value);
            }
            $result['data'] = $array;
            return $result;
        }

        if ($data instanceof NullLiteral) {
            $result['data'] = 'null';
            return $result;
        }

        if ($data instanceof BoolLiteral) {
            $result['data'] = $data->getValue() ? 'true' : 'false';
            return $result;
        }

        if ($data instanceof NumberLiteral) {
            $result['data'] = (string) $data->getValue();
            return $result;
        }

        if ($data instanceof StringLiteral) {
            $result['data'] = $data->getValue();
            return $result;
        }

        if ($data instanceof DateLiteral) {
            $result['data'] = $data->getValue()->format(DATE_ISO8601);
            return $result;
        }

        throw new \Exception('Broken structure for in local storage');
    }
}