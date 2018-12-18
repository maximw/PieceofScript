<?php


namespace PieceofScript\Services\Contexts;


use PieceofScript\Services\Errors\ContextStackEmptyException;

class ContextStack
{
    /** @var GlobalContext */
    protected $global;

    /** @var AbstractContext[] */
    protected $stack = [];

    /**
     * Push new Context to stack
     * @param AbstractContext $context
     * @throws \Exception
     */
    public function push(AbstractContext $context)
    {
        if ($context instanceof GlobalContext) {
            $this->global = $context;
        } else {
            $context->setGlobalContext($this->global);
            $context->setParentContext($this->head());
        }
        array_push($this->stack, $context);
    }

    /**
     * Return current Context
     *
     * @return AbstractContext
     * @throws ContextStackEmptyException
     */
    public function head(): AbstractContext
    {
        $context = end($this->stack);
        if (!$context instanceof AbstractContext) {
            throw new ContextStackEmptyException();
        }
        return $context;
    }

    /**
     * Return penultimate Context
     *
     * @return AbstractContext
     */
    public function neck(): AbstractContext
    {
        end($this->stack);
        return prev($this->stack);
    }

    /**
     * Get global Context
     *
     * @return AbstractContext
     * @throws \Exception
     */
    public function global(): GlobalContext
    {
        return $this->global;
    }

    /**
     * Pop Context from stack
     *
     * @return AbstractContext
     * @throws ContextStackEmptyException
     */
    public function pop(): AbstractContext
    {
        $context = array_pop($this->stack);
        if (!$context instanceof AbstractContext) {
            throw new ContextStackEmptyException();
        }
        return $context;
    }

}