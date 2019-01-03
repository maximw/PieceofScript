<?php


namespace PieceofScript\Services\Generators;


use PieceofScript\Services\Errors\InternalError;
use PieceofScript\Services\Errors\RuntimeError;
use PieceofScript\Services\Generators\Generators\FakerProvider;
use PieceofScript\Services\Generators\Generators\InternalProvider;
use PieceofScript\Services\Generators\Generators\JwtProvider;
use Symfony\Component\Yaml\Yaml;
use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Generators\Generators\BaseGenerator;
use PieceofScript\Services\Generators\Generators\InternalGenerator;
use PieceofScript\Services\Generators\Generators\YamlGenerator;
use PieceofScript\Services\Utils\Utils;
use PieceofScript\Services\Values\VariableName;


class GeneratorsRepository
{
    const DIRECTORY = 'generators';

    const ROOT_FILE = 'generators.yaml';

    /** @var IGenerator[] */
    protected $generators = [];

    /** @var IGeneratorProvider[]  */
    protected $providers = [];

    protected $files = [];

    public function __construct()
    {
        $rootFile = Config::get()->getGeneratorsFile();
        $directory = Config::get()->getGeneratorsDir();
        if (is_readable($rootFile)) {
            $this->files[] = $rootFile;
        }
        $this->files = array_merge($this->files, Utils::fileSearchInDir($directory, '*.yaml', true));

        $this->providers = [
            new InternalProvider(),
            new FakerProvider(),
            new JwtProvider(),
        ];

        $this->initGenerators();
    }

    /**
     * Fill generators repository
     */
    protected function initGenerators()
    {
        $this->initProviders();
        $this->initYamlGenerators();

    }

    /**
     * Init all YAML generators
     * @throws \Exception
     */
    public function initYamlGenerators()
    {
        foreach ($this->files as $file) {
            $yaml = Yaml::parseFile($file);

            foreach ($yaml as $generatorDefinition => $generatorValue) {
                $this->createYamlGenerator($generatorDefinition, $generatorValue, $file);
            }
        }
    }


    /**
     * @param string $generatorDefinition
     * @param $generatorBody
     * @param $fileName
     * @throws \Exception
     */
    protected function createYamlGenerator(string $generatorDefinition, $generatorBody, $fileName)
    {
        $generatorDefinition = trim($generatorDefinition);
        $flag = preg_match('~^([a-z][\\\\a-z0-9_]*)\s*(\([a-z0-9_,\\$\\s]*\))?$~i', $generatorDefinition, $matches);
        if (!$flag) {
            throw new \Exception('Error parsing generator definition ' . $generatorDefinition .  ' in ' . $fileName);
        }
        $generatorName = $matches[1];
        $id = $this->getGeneratorId($matches[1]);

        $generatorArguments = [];
        $matches[2] = trim($matches[2]);
        if (!empty($matches[2])) {
            //remove parentheses
            //TODO improve parsing generator arguments
            $arguments = trim(trim($matches[2]), '()');
            $arguments = explode(',', $arguments);
            foreach ($arguments as &$argument) {
                $argument = trim($argument);
                if (!empty($argument)) {
                    if (!preg_match('/^\$[a-z][a-z0-9_]*$/i', $argument)) {
                        throw new \Exception('Bad argument name in generator definition ' . $generatorDefinition . ' in ' . $fileName);
                    }
                    $generatorArguments[] = new VariableName($argument);
                }
            }

        }
        if ($this->exists($id)) {
            throw new \Exception('Duplicate generator name ' . $generatorDefinition);
        }

        $body = $generatorBody['body'] ?? null;
        $replace = $generatorBody['replace'] ?? null;
        $remove = $generatorBody['remove'] ?? null;

        if (null === $body) {
            throw new \Exception('Generator ' . $generatorName . ' has empty body');
        }

        $generator = new YamlGenerator($generatorName, $generatorArguments, $fileName);
        $generator->setBody($body)
            ->setReplace($replace)
            ->setRemove($remove);

        $this->generators[$id] = $generator;
    }

    protected function initProviders()
    {
        foreach ($this->providers as $provider) {
            $this->initProviderGenerators($provider);
        }
    }

    protected function initProviderGenerators(IGeneratorProvider $provider)
    {
        $generators = $provider->getGenerators();
        foreach ($generators as $generator) {
            $this->addInternalGenerator($generator);
        }
    }

    protected function addInternalGenerator(InternalGenerator $generator)
    {
        if (empty($generator::NAME)) {
            throw new InternalError('Internal generator has empty name ' . get_class($generator));
        }

        $name = $this->getGeneratorId($generator->getName());
        if (array_key_exists($name, $this->generators)) {
            throw new InternalError('Generator already exists ' .  get_class($generator));
        }

        $this->generators[$name] = $generator;
    }

    public function get(string $generatorName): BaseGenerator
    {
        $generatorName = $this->getGeneratorId($generatorName);
        if (!$this->exists($generatorName)) {
            throw new RuntimeError('Generator does not exist "' . $generatorName . '"');
        }
        return $this->generators[$generatorName];
    }

    public function add($generatorName, $generator)
    {
        $generatorName = $this->getGeneratorId($generatorName);
        if ($this->exists($generatorName)) {
            throw new \Exception('Generator does not exists "' . $generatorName . '"');
        }
        $this->generators[$generatorName] = $generator;
    }

    public function exists(string $generatorName): bool
    {
        $generatorName = $this->getGeneratorId($generatorName);
        return \array_key_exists($generatorName, $this->generators);
    }

    protected function getGeneratorId($generatorName)
    {
        return trim(mb_strtolower($generatorName, 'UTF-8'));
    }
}