<?php


namespace PieceofScript\Services\Endpoints;


use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Errors\Endpoints\EndpointDefinitionError;
use PieceofScript\Services\Values\StringLiteral;
use Symfony\Component\Yaml\Yaml;
use PieceofScript\Services\Utils\Utils;
use PieceofScript\Services\Values\VariableName;

class EndpointsRepository
{
    const ARGUMENT_PLACEHOLDER = 'f132d59f3587463b99e3262a8b5a7975'; //random v4 UUID
    const DIRECTORY = 'endpoints';
    const ROOT_FILE = 'endpoints.yaml';

    /** @var Endpoint[] */
    protected $endpoints = [];

    protected $files = [];

    public function __construct()
    {
        $rootFile = Config::get()->getEndpointsFile();
        $directory = Config::get()->getEndpointsDir();
        if (is_readable($rootFile)) {
            $this->files[] = $rootFile;
        }
        $this->files = array_merge($this->files, Utils::fileSearchInDir($directory, '*.yaml', true));
        $this->initEndpoints();
    }

    protected function initEndpoints()
    {
        foreach ($this->files as $file) {
            $yaml = Yaml::parseFile($file);

            foreach ($yaml as $endpointName => $endpointBody) {
                $this->createEndpoint($endpointName, $endpointBody, $file);
            }
        }
    }

    /**
     * @param string $endpointName
     * @param array $endpointBody
     * @param string $fileName
     * @throws EndpointDefinitionError
     */
    protected function createEndpoint(string $endpointName, array $endpointBody, string $fileName)
    {
        $normalizedName = $this->normalizeName($endpointName);

        if ($this->get($normalizedName) instanceof Endpoint) {
            throw new EndpointDefinitionError('Error: Duplicate endpoint name.', $endpointName, $fileName);
        }

        if (empty($endpointBody)) {
            throw new EndpointDefinitionError('Error: empty endpoint body.', $endpointName, $fileName);
        }

        if (empty($endpointBody['method'])) {
            throw new EndpointDefinitionError('HTTP method is required.', $endpointName, $fileName);
        }

        if (empty($endpointBody['url'])) {
            throw new EndpointDefinitionError('URL is required.', $endpointName, $fileName);
        }

        $method = $endpointBody['method'];
        $url = $endpointBody['url'];
        $headers = $endpointBody['headers'] ?? [];
        $cookies = $endpointBody['cookies'] ?? [];
        $auth = $endpointBody['auth'] ?? [];
        $query = $endpointBody['query'] ?? [];
        $format = $endpointBody['format'] ?? new StringLiteral(Endpoint::FORMAT_NONE);
        $data = $endpointBody['data'] ?? [];
        $before = $endpointBody['before'] ?? [];
        $after = $endpointBody['after'] ?? [];

        if (is_string($before)) {
            $before = explode("\n", $before);
        }
        if (is_string($after)) {
            $after = explode("\n", $after);
        }

        $arguments = $this->extractArguments($endpointName);

        $this->endpoints[$normalizedName] = new Endpoint($normalizedName, $fileName);
        $this->endpoints[$normalizedName]
            ->setOriginalName($endpointName)
            ->setArguments($arguments)
            ->setHttpMethod($method)
            ->setUrl($url)
            ->setHeaders($headers)
            ->setCookies($cookies)
            ->setAuth($auth)
            ->setQuery($query)
            ->setFormat($format)
            ->setData($data)
            ->setBefore($before)
            ->setAfter($after);
    }

    /**
     * Returns Endpoint wrapped by EndpointCall with given parameters when it is called
     *
     * @param string $endpointCallExpression
     * @return EndpointCall
     * @throws \Exception
     */
    public function getByCall(string $endpointCallExpression): EndpointCall
    {
        foreach ($this->endpoints as $endpointName => $endpoint) {
            $parameters = $this->match($endpointCallExpression, $endpointName);
            if (false !== $parameters) {
                $endpointCall = new EndpointCall($endpoint, $parameters);
                return $endpointCall;
            }
        }

        throw new \Exception('Endpoint not found ' . $endpointCallExpression);
    }

    public function getCount(): int
    {
        return count($this->endpoints);
    }

    protected function get(string $normalizedName)
    {
        return $this->endpoints[$normalizedName] ?? null;
    }

    /**
     * Normalize Endpoints's name
     *
     * @param string $endpointName
     * @return string
     * @throws \Exception
     */
    protected function normalizeName(string $endpointName): string
    {
        if (strpos($endpointName, self::ARGUMENT_PLACEHOLDER) !== false) {
            throw new \Exception(self::ARGUMENT_PLACEHOLDER.' is not allowed in endpoint definition');
        }
        $endpointName = trim($endpointName);
        $endpointName = preg_replace('/\s\s+/i', ' ', $endpointName);
        $endpointName = preg_replace('/\s*(\$[a-z][a-z0-9_]*)\s*/i', self::ARGUMENT_PLACEHOLDER, $endpointName);
        $endpointName = strtolower($endpointName);
        return $endpointName;
    }

    /**
     * Returns arguments names from Endpoint name
     *
     * @param string $endpointName
     * @return array
     * @throws \Exception
     */
    protected function extractArguments(string $endpointName): array
    {
        if (strpos($endpointName, self::ARGUMENT_PLACEHOLDER) !== false) {
            throw new \Exception(self::ARGUMENT_PLACEHOLDER.' is not allowed in endpoint definition');
        }
        $endpointName = trim($endpointName);
        $endpointName = preg_replace('/\s\s+/i', ' ', $endpointName);
        preg_match_all('/\s*(\$[a-z][a-z0-9_]*)\s*/i', $endpointName, $matches);

        $arguments = $matches[1] ?? [];
        foreach ($arguments as &$argument) {
            $argument = new VariableName($argument);
        }

        return $arguments;
    }

    /**
     * Match Endpoint's call string and given Endpoint name
     * Returns parameters expressions or false if not matched
     *
     * @param string $endpointCallExpression
     * @param string $endpointName
     * @return bool|array
     */
    protected function match(string $endpointCallExpression, string $endpointName)
    {
        $regexp = str_replace(self::ARGUMENT_PLACEHOLDER, '\s+(\$.+)\s*', preg_quote($endpointName));
        $regexp = '/^' . str_replace(' ', '\s+', $regexp) . '$/i';
        $flag = preg_match($regexp, $endpointCallExpression, $matches);

        if (!$flag) {
            return false;
        }
        array_shift($matches);
        return $matches;
    }
}