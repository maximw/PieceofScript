<?php


namespace PieceofScript\Services\Endpoints;


use PieceofScript\Services\Call\BaseCall;

class Endpoint
{
    const FORMAT_NONE = 'none'; // No data
    const FORMAT_JSON = 'json'; // Json body
    const FORMAT_RAW = 'raw';   // Raw string
    const FORMAT_FROM = 'form'; // application/x-www-form-urlencoded
    const FORMAT_MULTIPART = 'multipart'; // multipart/form-data

    const FORMATS = [
        self::FORMAT_NONE,
        self::FORMAT_JSON,
        self::FORMAT_RAW,
        self::FORMAT_FROM,
        self::FORMAT_MULTIPART,
    ];

    /**
     * @var BaseCall
     */
    protected $definition;

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

    public function __construct(BaseCall $definition, string $file)
    {
        $this->setDefinition($definition);
        $this->setFile($file);
    }

    /**
     * @return BaseCall
     */
    public function getDefinition(): BaseCall
    {
        return $this->definition;
    }

    /**
     * @param BaseCall $definition
     * @return Endpoint
     */
    public function setDefinition(BaseCall $definition): Endpoint
    {
        $this->definition = $definition;
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
     * @return string|string[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string|string[] $headers
     * @return Endpoint
     */
    public function setHeaders($headers): Endpoint
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return string|string[]
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @param string|string[] $cookies
     * @return Endpoint
     */
    public function setCookies($cookies): Endpoint
    {
        $this->cookies = $cookies;
        return $this;
    }

    /**
     * @return string|string[]
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @param string|string[] $auth
     * @return Endpoint
     */
    public function setAuth($auth): Endpoint
    {
        $this->auth = $auth;
        return $this;
    }

    /**
     * @return string|string[]
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string|string[] $query
     * @return Endpoint
     */
    public function setQuery($query): Endpoint
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
     * @return string|string[]
     */
    public function getBefore(): array
    {
        return $this->before;
    }

    /**
     * @param string|string[] $before
     * @return Endpoint
     */
    public function setBefore($before): Endpoint
    {
        $this->before = $before;
        return $this;
    }

    /**
     * @return string|string[]
     */
    public function getAfter(): array
    {
        return $this->after;
    }

    /**
     * @param string|string[] $after
     * @return Endpoint
     */
    public function setAfter($after): Endpoint
    {
        $this->after = $after;
        return $this;
    }


}