<?php


namespace PieceofScript\Services\Call;


use PieceofScript\Services\Parsing\Token;
use PieceofScript\Services\Parsing\TokensQueue;

class ArgumentCallItem extends BaseCallItem
{
    protected $byReference = true;

    public function __construct(TokensQueue $value = null, $byReference = true)
    {
        if (null === $value) {
            $value = new TokensQueue();
        }
        parent::__construct($value);
        $this->byReference = $byReference;
    }

    public function addToken(Token $token): self
    {
        $this->value->add($token);
        return $this;
    }

    public function getValue(): TokensQueue
    {
        return $this->value;
    }

    public function setValue(TokensQueue $value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function isByReference(): bool
    {
        return $this->byReference;
    }

    /**
     * @param bool $byReference
     * @return ArgumentCallItem
     */
    public function setByReference(bool $byReference): ArgumentCallItem
    {
        $this->byReference = $byReference;
        return $this;
    }

}