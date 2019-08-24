<?php


namespace PieceofScript\Services\Call;


class BaseCall
{
    /** @var string */
    protected $originalString;

    /** @var BaseCallItem[]  */
    protected $items = [];

    /** @var ArgumentCallItem[]|null */
    protected $arguments = null;

    /** @var OptionsCallItem[] */
    protected $options = [];

    public function __construct(string $originalString = '', array $items = [])
    {
        $this->originalString = $originalString;
        $this->items = $items;
    }

    public function isEqual(BaseCall $call): bool
    {
        if (count($this->getItems()) !== count($call->getItems())) {
            return false;
        }

        foreach ($call->getItems() as $key => $item) {
            if (!$item->isEqual($this->items[$key]))  {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string
     */
    public function getOriginalString(): string
    {
        return $this->originalString;
    }

    /**
     * @param string $originalString
     * @return BaseCall
     */
    public function setOriginalString(string $originalString): BaseCall
    {
        $this->originalString = $originalString;
        return $this;
    }

    /**
     * @return ArgumentCallItem[]
     */
    public function getArguments(): array
    {
        if (is_array($this->arguments)) {
            return $this->arguments;
        }
        $this->arguments = [];
        foreach ($this->items as $item) {
            if ($item instanceof ArgumentCallItem) {
                $this->arguments[] = $item;
            }
        }

        return $this->arguments;
    }

    /**
     * @return OptionsCallItem[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param OptionsCallItem[] $options
     * @return BaseCall
     */
    public function setOptions(array $options): BaseCall
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return BaseCallItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param BaseCallItem[] $items
     * @return BaseCall
     */
    public function setItems(array $items)
    {
        $this->items = array_values($items);
        return $this;
    }

    public function addItem(BaseCallItem $item)
    {
        if ($item instanceof OptionsCallItem) {
            $this->options[] = $item;
        } elseif ($item instanceof TextCallItem && $this->getLastItem() instanceof TextCallItem) {
            $this->getLastItem()->merge($item);
        } else {
            $this->items[] = $item;
        }
        return $this;
    }

    protected function getLastItem(): ?BaseCallItem
    {
        $item = end($this->items);
        return $item instanceof BaseCallItem ? $item : null;
    }
}

