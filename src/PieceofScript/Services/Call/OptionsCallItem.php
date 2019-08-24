<?php


namespace PieceofScript\Services\Call;


use PieceofScript\Services\Parsing\Token;
use PieceofScript\Services\Parsing\TokensQueue;

class OptionsCallItem extends BaseCallItem
{

    public function __construct(TokensQueue $value = null)
    {
        if (null === $value) {
            $value = new TokensQueue();
        }
        parent::__construct($value);
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

}