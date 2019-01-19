<?php


namespace PieceofScript\Services\Out;


use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class In
{

    /** @var OutputInterface */
    protected static $output;

    /** @var InputInterface */
    protected static $input;

    /** @var QuestionHelper */
    protected static $helper;

    public static function init(InputInterface $input, OutputInterface $output, QuestionHelper $helper)
    {
        static::$input = $input;
        static::$output = $output;
        static::$helper = $helper;
    }

    public static function pressEnter(float $seconds)
    {
        if (static::$input->isInteractive()) {
            $question = new ConfirmationQuestion('Pause || Press Enter to continue...', false);
            static::$helper->ask(static::$input, static::$output, $question);
        } else {
            usleep((int) ($seconds * 1000000));
        }
    }

}