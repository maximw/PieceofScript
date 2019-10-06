<?php


namespace PieceofScript\Services\Endpoints;


use function DeepCopy\deep_copy;
use PieceofScript\Services\Call\BaseCall;
use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Errors\Endpoints\EndpointDefinitionError;
use PieceofScript\Services\Parsing\CallLexer;
use PieceofScript\Services\Values\StringLiteral;
use Symfony\Component\Yaml\Yaml;
use PieceofScript\Services\Utils\Utils;

class EndpointsRepository
{
    /** @var Endpoint[] */
    protected $endpoints = [];

    protected $files = [];

    /** @var CallLexer */
    protected $callLexer;

    public function __construct(CallLexer $callLexer)
    {
        $this->callLexer = $callLexer;
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
     * @param string $endpointString
     * @param array $endpointBody
     * @param string $fileName
     * @throws EndpointDefinitionError
     */
    protected function createEndpoint(string $endpointString, array $endpointBody, string $fileName)
    {
        $definition = $this->callLexer->getCall($endpointString);

        if ($this->getByCall($definition) instanceof Endpoint) {
            throw new EndpointDefinitionError('Error: Duplicate endpoint name.', $endpointString, $fileName);
        }

        if (empty($endpointBody)) {
            throw new EndpointDefinitionError('Error: empty endpoint body.', $endpointString, $fileName);
        }

        if (empty($endpointBody['method'])) {
            throw new EndpointDefinitionError('HTTP method is required.', $endpointString, $fileName);
        }

        if (empty($endpointBody['url'])) {
            throw new EndpointDefinitionError('URL is required.', $endpointString, $fileName);
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

        $endpoint = new Endpoint($definition, $fileName);
        $endpoint->setHttpMethod($method)
            ->setUrl($url)
            ->setHeaders($headers)
            ->setCookies($cookies)
            ->setAuth($auth)
            ->setQuery($query)
            ->setFormat($format)
            ->setData($data)
            ->setBefore($before)
            ->setAfter($after);

        $this->endpoints[] = $endpoint;
    }

    /**
     * Returns Endpoint by Call object
     *
     * @param BaseCall $call
     * @return Endpoint|null
     */
    public function getByCall(BaseCall $call)
    {
        foreach ($this->endpoints as $endpoint) {
            if ($endpoint->getDefinition()->isEqual($call)) {
                return deep_copy($endpoint);
            }
        }

        return null;
    }

    /**
     * @return Endpoint[]
     */
    public function getAll(): array
    {
        return $this->endpoints;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return count($this->endpoints);
    }

}