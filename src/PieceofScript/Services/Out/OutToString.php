<?php


namespace PieceofScript\Services\Out;



class OutToString extends Out
{
    const FORMATTING = false;

    protected static $buffer = '';

    public static function getBuffer(): string
    {
        $buffer = static::$buffer;
        static::$buffer = '';
        return $buffer;
    }

    protected static function writeln($text, int $verbosity, int $indent = 0)
    {
        $parts = explode("\n", $text);
        foreach ($parts as $part) {
            static::$buffer .= str_repeat(' ', $indent * self::INDENT) . $part . "\n";
        }
    }

    protected static function write($text, int $verbosity, int $indent = 0)
    {
        static::$buffer .= str_repeat(' ', $indent * self::INDENT) . $text;
    }

}