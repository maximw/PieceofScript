<?php


namespace PieceofScript\Services\Statistics;




use PieceofScript\Services\Contexts\AbstractContext;
use PieceofScript\Services\Variables\VariablesRepository;

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

    /** @var bool */
    protected $status;

    /** @var VariablesRepository */
    protected $variables;

    public function __construct(
        string $code,
        string $file,
        int $line,
        bool $status,
        VariablesRepository $variablesDump
    )
    {
        $this->code = $code;
        $this->file = $file;
        $this->line = $line;
        $this->status = $status;
        $this->variables = $variablesDump;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getLine(): string
    {
        return $this->line;
    }

    /**
     * @return bool
     */
    public function getStatus(): bool
    {
        return $this->status;
    }

    public function getVariables(): VariablesRepository
    {
        return $this->variables;
    }


}