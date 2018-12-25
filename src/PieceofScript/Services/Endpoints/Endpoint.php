<?php


namespace PieceofScript\Services\Endpoints;


class Endpoint
{
    const FORMAT_NONE = 'none'; // No data
    const FORMAT_JSON = 'json'; // Json body
    const FORMAT_FROM = 'form'; // application/x-www-form-urlencoded
    const FORMAT_MULTIPART = 'multipart'; // multipart/form-data

    const FORMATS = [
        self::FORMAT_NONE,
        self::FORMAT_JSON,
        self::FORMAT_FROM,
        self::FORMAT_MULTIPART,
    ];

    /**
     * Name of Endpoint
     * @var string
     */
    protected $name;

    /**
     * Arguments names of endpoint
     * @var string[]
     */
    protected $arguments = [];

    /**
     * Original name for reports
     * @var string
     */
    protected $originalName;

    /**
     * File name where endpoint was declared
     * @var string
     */
    protected $file;

    /**
     * HTTP method
     * @var string
     */
    protected $httpMethod;

    /**
     * Url expression
     * @var string
     */
    protected $url;

    /**
     * Array  [Header-Name => Header expression]
     * @var array
     */
    protected $headers = [];

    /**
     * Array  [cookie_name => Cookie expression]
     * @var array
     */
    protected $cookies = [];

    /**
     * Array  [login, password, type]
     * @var array
     */
    protected $auth = [];

    /**
     * Array  [param_name => Parameter expression]
     * @var array
     */
    protected $query = [];

    /**
     *
     * @var string
     */
    protected $format = self::FORMAT_NONE;

    /**
     * Data to send according to format
     * @var mixed
     */
    protected $data;

    /**
     * Commands executed before HTTP request
     * @var string[]
     */
    protected $before;

    /**
     * Commands executed after HTTP request
     * @var string[]
     */
    protected $after;

    public function __construct(string $name, string $file)
    {
        $this->setName($name);
        $this->setFile($file);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Endpoint
     */
    public function setName(string $name): Endpoint
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    /**
     * @param string $originalName
     * @return Endpoint
     */
    public function setOriginalName(string $originalName): Endpoint
    {
        $this->originalName = $originalName;
        return $this;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     * @return Endpoint
     */
    public function setArguments(array $arguments): Endpoint
    {
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @param string $file
     * @return Endpoint
     */
    public function setFile(string $file): Endpoint
    {
        $this->file = $file;
        return $this;
    }

    /**
     * @return string
     */
    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    /**
     * @param string $httpMethod
     * @return Endpoint
     */
    public function setHttpMethod(string $httpMethod): Endpoint
    {
        $this->httpMethod = $httpMethod;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Endpoint
     */
    public function setUrl(string $url): Endpoint
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return Endpoint
     */
    public function setHeaders(array $headers): Endpoint
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return array
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    /**
     * @param array $cookies
     * @return Endpoint
     */
    public function setCookies(array $cookies): Endpoint
    {
        $this->cookies = $cookies;
        return $this;
    }

    /**
     * @return array
     */
    public function getAuth(): array
    {
        return $this->auth;
    }

    /**
     * @param array $auth
     * @return Endpoint
     */
    public function setAuth(array $auth): Endpoint
    {
        $this->auth = $auth;
        return $this;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @param array $query
     * @return Endpoint
     */
    public function setQuery(array $query): Endpoint
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $format
     * @return Endpoint
     * @throws \Exception
     */
    public function setFormat($format): Endpoint
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return Endpoint
     */
    public function setData($data): Endpoint
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getBefore(): array
    {
        return $this->before;
    }

    /**
     * @param string[] $before
     * @return Endpoint
     */
    public function setBefore(array $before): Endpoint
    {
        $this->before = $before;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getAfter(): array
    {
        return $this->after;
    }

    /**
     * @param string[] $after
     * @return Endpoint
     */
    public function setAfter(array $after): Endpoint
    {
        $this->after = $after;
        return $this;
    }


}