<?php

namespace PieceofScript\Services\Parsing;

class Token
{
    const MAX_PRIORITY = PHP_INT_MAX;

    const T_GENERATOR_NAME = 'T_GENERATOR_NAME';
    const T_VARIABLE       = 'T_VARIABLE';
    const T_VARIABLE_TYPE  = 'T_VARIABLE_TYPE';
    const T_ASSIGNMENT     = 'T_ASSIGNMENT';
    const T_ARRAY_KEY      = 'T_ARRAY_KEY';
    const T_ARRAY_SUB_AST  = 'T_ARRAY_SUB_AST';
    const T_ARGUMENTS_END  = 'T_ARGUMENTS_END';

    const T_STRING_DOUBLE = 'T_STRING_DOUBLE';
    const T_STRING = 'T_STRING';
    const T_NUMBER = 'T_NUMBER';
    const T_BOOL   = 'T_BOOL';
    const T_NULL   = 'T_NULL';

    const T_CLOSE_PARENTHESIS = 'T_CLOSE_PARENTHESIS';
    const T_OPEN_PARENTHESIS  = 'T_OPEN_PARENTHESIS';
    const T_CLOSE_BRACKETS    = 'T_CLOSE_BRACKETS';
    const T_OPEN_BRACKETS     = 'T_OPEN_BRACKETS';
    const T_COMMA             = 'T_COMMA';
    const T_SPACE             = 'T_SPACE';
    const T_COMMENT           = 'T_COMMENT';
    const T_SEMICOLON         = 'T_SEMICOLON';

    const T_EQUALS            = 'T_EQUALS';
    const T_GREATER_THAN      = 'T_GREATER_THAN';
    const T_LOWER_THAN        = 'T_LOWER_THAN';
    const T_NOT_EQUALS        = 'T_NOT_EQUALS';
    const T_GREATER_EQUAL     = 'T_GREATER_EQUAL';
    const T_LOWER_EQUAL       = 'T_LOWER_EQUAL';
    const T_PLUS              = 'T_PLUS';
    const T_MINUS             = 'T_MINUS';
    const T_MULTIPLY          = 'T_MULTIPLY';
    const T_POSITIVE          = 'T_POSITIVE';
    const T_NEGATIVE          = 'T_NEGATIVE';
    const T_DIVIDE            = 'T_DIVIDE';
    const T_DIVIDE_MOD        = 'T_DIVIDE_MOD';
    const T_POWER             = 'T_POWER';
    const T_OR                = 'T_OR';
    const T_AND               = 'T_AND';
    const T_NOT               = 'T_NOT';

    const T_CALL_ARGUMENT_BRACKET_OPEN = 'T_CALL_ARGUMENT_BRACKET_OPEN';
    const T_CALL_ARGUMENT_BRACKET_CLOSE = 'T_CALL_ARGUMENT_BRACKET_CLOSE';
    const T_CALL_ARGUMENT_DOUBLE_BRACKET_OPEN = 'T_CALL_ARGUMENT_DOUBLE_BRACKET_OPEN';
    const T_CALL_ARGUMENT_DOUBLE_BRACKET_CLOSE = 'T_CALL_ARGUMENT_DOUBLE_BRACKET_CLOSE';
    const T_CALL_WORD = 'T_CALL_WORD';

    const TYPE_VALUE = 'val';
    const TYPE_VARIABLE = 'var';
    const TYPE_FUNCTION = 'fun';
    const TYPE_ASSIGNMENT = '=';
    const TYPE_ARRAY_KEY = 'key';
    const TYPE_COMMA = ',';
    const TYPE_OPERATION = 'op';
    const TYPE_OPEN_PARENTHESIS = '(';
    const TYPE_CLOSE_PARENTHESIS = ')';
    const TYPE_OPEN_BRACKETS = '[';
    const TYPE_CLOSE_BRACKETS = ']';
    const TYPE_ARGUMENTS_END = '|';
    const TYPE_SUB_AST = 'sub';
    const TYPE_IGNORED = 'ignore';
    const TYPE_SEMICOLON = ';';
    const TYPE_CALL_SYNTAX = 'call';

    const PRIORITIES = [
        self::T_GENERATOR_NAME => Token::MAX_PRIORITY,
        self::T_VARIABLE       => Token::MAX_PRIORITY,
        self::T_VARIABLE_TYPE  => Token::MAX_PRIORITY,
        self::T_ASSIGNMENT     => -1,
        self::T_ARRAY_KEY      => Token::MAX_PRIORITY,
        self::T_ARRAY_SUB_AST  => Token::MAX_PRIORITY,
        self::T_ARGUMENTS_END  => Token::MAX_PRIORITY,

        self::T_STRING_DOUBLE => Token::MAX_PRIORITY,
        self::T_STRING => Token::MAX_PRIORITY,
        self::T_NUMBER => Token::MAX_PRIORITY,
        self::T_BOOL   => Token::MAX_PRIORITY,
        self::T_NULL   => Token::MAX_PRIORITY,

        self::T_CLOSE_PARENTHESIS => 0,
        self::T_OPEN_PARENTHESIS  => 0,
        self::T_CLOSE_BRACKETS    => 0,
        self::T_OPEN_BRACKETS     => 0,
        self::T_COMMA             => 0,
        self::T_SPACE             => 0,
        self::T_COMMENT           => 0,
        self::T_SEMICOLON         => 0,

        self::T_EQUALS            => 3,
        self::T_GREATER_THAN      => 3,
        self::T_LOWER_THAN        => 3,
        self::T_NOT_EQUALS        => 3,
        self::T_GREATER_EQUAL     => 3,
        self::T_LOWER_EQUAL       => 3,
        self::T_PLUS              => 4,
        self::T_MINUS             => 4,
        self::T_MULTIPLY          => 5,
        self::T_POSITIVE          => 8,
        self::T_NEGATIVE          => 8,
        self::T_DIVIDE            => 5,
        self::T_DIVIDE_MOD        => 5,
        self::T_POWER             => 6,
        self::T_OR                => 1,
        self::T_AND               => 2,
        self::T_NOT               => 7,
    ];

    const TYPES = [
        self::T_GENERATOR_NAME => self::TYPE_FUNCTION,
        self::T_VARIABLE       => self::TYPE_VARIABLE,
        self::T_VARIABLE_TYPE  => self::TYPE_VARIABLE,
        self::T_ASSIGNMENT     => self::TYPE_ASSIGNMENT,
        self::T_ARRAY_KEY      => self::TYPE_ARRAY_KEY,
        self::T_ARRAY_SUB_AST  => self::TYPE_ARRAY_KEY,
        self::T_ARGUMENTS_END  => self::TYPE_ARGUMENTS_END,

        self::T_STRING_DOUBLE => self::TYPE_VALUE,
        self::T_STRING => self::TYPE_VALUE,
        self::T_NUMBER => self::TYPE_VALUE,
        self::T_BOOL   => self::TYPE_VALUE,
        self::T_NULL   => self::TYPE_VALUE,

        self::T_CLOSE_PARENTHESIS => self::TYPE_CLOSE_PARENTHESIS,
        self::T_OPEN_PARENTHESIS  => self::TYPE_OPEN_PARENTHESIS,
        self::T_CLOSE_BRACKETS    => self::TYPE_CLOSE_BRACKETS,
        self::T_OPEN_BRACKETS     => self::TYPE_OPEN_BRACKETS,
        self::T_COMMA             => self::TYPE_COMMA,
        self::T_SPACE             => self::TYPE_IGNORED,
        self::T_COMMENT           => self::TYPE_IGNORED,
        self::T_SEMICOLON         => self::TYPE_SEMICOLON,

        self::T_EQUALS            => self::TYPE_OPERATION,
        self::T_GREATER_THAN      => self::TYPE_OPERATION,
        self::T_LOWER_THAN        => self::TYPE_OPERATION,
        self::T_NOT_EQUALS        => self::TYPE_OPERATION,
        self::T_GREATER_EQUAL     => self::TYPE_OPERATION,
        self::T_LOWER_EQUAL       => self::TYPE_OPERATION,
        self::T_PLUS              => self::TYPE_OPERATION,
        self::T_MINUS             => self::TYPE_OPERATION,
        self::T_MULTIPLY          => self::TYPE_OPERATION,
        self::T_POSITIVE          => self::TYPE_OPERATION,
        self::T_NEGATIVE          => self::TYPE_OPERATION,
        self::T_DIVIDE            => self::TYPE_OPERATION,
        self::T_DIVIDE_MOD        => self::TYPE_OPERATION,
        self::T_POWER             => self::TYPE_OPERATION,
        self::T_OR                => self::TYPE_OPERATION,
        self::T_AND               => self::TYPE_OPERATION,
        self::T_NOT               => self::TYPE_OPERATION,

        self::T_CALL_ARGUMENT_BRACKET_OPEN => self::TYPE_CALL_SYNTAX,
        self::T_CALL_ARGUMENT_BRACKET_CLOSE => self::TYPE_CALL_SYNTAX,
        self::T_CALL_ARGUMENT_DOUBLE_BRACKET_OPEN => self::TYPE_CALL_SYNTAX,
        self::T_CALL_ARGUMENT_DOUBLE_BRACKET_CLOSE => self::TYPE_CALL_SYNTAX,
        self::T_CALL_WORD => self::TYPE_CALL_SYNTAX,
    ];

    const ARGUMENTS_COUNT = [
        self::T_GENERATOR_NAME => 0,
        self::T_VARIABLE       => 0,
        self::T_VARIABLE_TYPE  => 0,
        self::T_ASSIGNMENT     => 2,
        self::T_ARRAY_KEY      => 1,
        self::T_ARRAY_SUB_AST  => 0,
        self::T_ARGUMENTS_END  => 0,

        self::T_STRING_DOUBLE => 0,
        self::T_STRING => 0,
        self::T_NUMBER => 0,
        self::T_BOOL   => 0,
        self::T_NULL   => 0,

        self::T_CLOSE_PARENTHESIS => 0,
        self::T_OPEN_PARENTHESIS  => 0,
        self::T_CLOSE_BRACKETS    => 0,
        self::T_OPEN_BRACKETS     => 0,
        self::T_COMMA             => 0,
        self::T_SPACE             => 0,
        self::T_COMMENT           => 0,
        self::T_SEMICOLON         => 0,

        self::T_EQUALS            => 2,
        self::T_GREATER_THAN      => 2,
        self::T_LOWER_THAN        => 2,
        self::T_NOT_EQUALS        => 2,
        self::T_GREATER_EQUAL     => 2,
        self::T_LOWER_EQUAL       => 2,
        self::T_PLUS              => 2,
        self::T_MINUS             => 2,
        self::T_MULTIPLY          => 2,
        self::T_NEGATIVE          => 1,
        self::T_POSITIVE          => 1,
        self::T_DIVIDE            => 2,
        self::T_DIVIDE_MOD        => 2,
        self::T_POWER             => 2,
        self::T_OR                => 2,
        self::T_AND               => 2,
        self::T_NOT               => 1,
    ];


    /** @var string */
    protected $name;

    /** @var mixed */
    protected $value;

    /** @var int|string */
    protected $priority;

    /** @var string */
    protected $type;

    /** @var int */
    protected $offset;

    /** @var int */
    protected $argumentsCount;

    public function __construct(string $name, $value, int $offset)
    {
        $this->setName($name);
        $this->setValue($value);
        $this->setType(self::TYPES[$name]);
        $this->setOffset($offset);
        $this->setPriority(self::PRIORITIES[$name] ?? 0);
        $this->setArgumentsCount(self::ARGUMENTS_COUNT[$name] ?? 0);
    }


    public function morePriority(Token $token)
    {
        if (self::MAX_PRIORITY === $this->priority) {
            return true;
        }
        if (self::MAX_PRIORITY === $token->priority) {
            return false;
        }
        return $this->priority > $token->priority;
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
     * @return Token
     */
    public function setName(string $name): Token
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return Token
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int|string $priority
     * @return Token
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Token
     */
    public function setType(string $type): Token
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     * @return Token
     */
    public function setOffset(int $offset): Token
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @return int
     */
    public function getArgumentsCount(): int
    {
        return $this->argumentsCount;
    }

    /**
     * @param int $argumentsCount
     * @return Token
     */
    public function setArgumentsCount(int $argumentsCount): Token
    {
        $this->argumentsCount = $argumentsCount;
        return $this;
    }


}