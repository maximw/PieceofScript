<?php


namespace PieceofScript\Services\Statistics;


use PieceofScript\Services\Values\ArrayLiteral;

class StatAssertion
{
    const STATUS_OK = 'ok';
    const STATUS_FAILED = 'failed';
    const STATUS_ERROR = 'error';

    /** @var string */
    protected $code;

    /** @var string */
    protected $file;

    /** @var string */
    protected $line;

    /** @var string */
    protected $status;

    /** @var array */
    protected $request;

    /** @var array */
    protected $response;

    public function __construct(
        string $code,
        string $file,
        int $line,
        string $status,
        array $request,
        array $response
    )
    {
        $this->code = $code;
        $this->file = $file;
        $this->line = $line;
        $this->status = $status;
        $this->request = $request;
        $this->response = $response;
    }

}