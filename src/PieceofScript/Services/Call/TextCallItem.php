<?php


namespace PieceofScript\Services\Call;


use PieceofScript\Services\Parsing\Token;

class TextCallItem extends BaseCallItem
{

    public function __construct(string $value = '')
    {
        $this->value = self::normalize($value);
    }

    public function isEqual(BaseCallItem $callItem): bool
    {
        if (!parent::isEqual($callItem)) {
            return false;
        }

        return $this->getValue() === $callItem->getValue();
    }

    public function addToken(Token $token): self
    {
        $this->value = $this->value . $token->getValue();
        return $this;
    }

    public function getValue(): string
    {
        return self::normalize($this->value);
    }

    public function merge(TextCallItem $callItem)
    {
        $this->value = $this->value . $callItem->value;
    }

    protected static function normalize(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/\s\s+/i', ' ', $value);
        $value = mb_strtolower($value, 'UTF-8');
        return $value;
    }



}