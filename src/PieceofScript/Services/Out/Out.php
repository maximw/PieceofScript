<?php


namespace PieceofScript\Services\Out;


use PieceofScript\Services\Contexts\AbstractContext;
use PieceofScript\Services\Contexts\ContextStack;
use PieceofScript\Services\Contexts\EndpointContext;
use PieceofScript\Services\Contexts\GlobalContext;
use PieceofScript\Services\Contexts\TestcaseContext;
use PieceofScript\Services\Errors\InternalError;
use PieceofScript\Services\Errors\RuntimeError;
use PieceofScript\Services\Utils\Utils;
use Symfony\Component\Console\Output\OutputInterface;

class Out
{
    const STYLES = [
        'error' => '',
    ];

    /** @var OutputInterface */
    protected static $output;

    public static function setOutput(OutputInterface $output)
    {
        static::$output = $output;
    }

    public static function printError(InternalError $e, ContextStack $contextStack = null)
    {
        $verbosity = OutputInterface::VERBOSITY_NORMAL;
        static::$output->writeln('<fg=white;bg=red>Error:</> ' . $e->getMessage(), $verbosity);
        if ($e instanceof RuntimeError && null !== $contextStack) {
            static::printContextStack($contextStack);
        }
    }

    public static function printWarning(string $message, ContextStack $contextStack = null)
    {
        $verbosity = OutputInterface::VERBOSITY_VERY_VERBOSE;
        static::$output->writeln('<fg=yellow>Warning:</> ' . $message, $verbosity);
        if (null !== $contextStack) {
            $context = $contextStack->head();
            static::$output->writeln('in context "' . $context->getName() . '", file "' . $context->getFile() . '" at line ' . ($context->getLine() + 1), $verbosity);
        }
    }

    public static function printContextStack(ContextStack $contextStack)
    {
        $verbosity = OutputInterface::VERBOSITY_NORMAL;

        static::$output->writeln('Call stack:', $verbosity);

        /** @var AbstractContext $context */
        $context = $contextStack->head();
        do {
            $text = 'Context "' . $context->getName() . '"';
            if ($context->getFile()) {
                $text = $text . ', file "' . $context->getFile() . '"';
            }
            if ($context->getLine() !== null) {
                 $text = $text . ' at line ' . ($context->getLine() + 1);
            }
            static::$output->writeln($text, $verbosity);
            $context = $context->getParentContext();
        } while (null !== $context);
    }


    public static function printValues(array $values)
    {
        $verbosity = OutputInterface::VERBOSITY_VERBOSE;
        foreach ($values as $value) {
            static::$output->write('<fg=blue>'.$value.'</>', false, $verbosity);
        }

        static::$output->writeln('', $verbosity);
    }

    public static function printLine(string $line)
    {
        $line = trim($line, PHP_EOL);
        $verbosity = OutputInterface::VERBOSITY_DEBUG;
        static::$output->writeln($line, $verbosity);
    }

    public static function printCancel()
    {
        $verbosity = OutputInterface::VERBOSITY_DEBUG;
        static::$output->writeln('Testing is cancelled', $verbosity);
    }

    public static function printAssert(string $code, bool $success)
    {
        if ($success) {
            $verbosity = OutputInterface::VERBOSITY_DEBUG;
            static::$output->writeln('Assert: "' . trim($code) . '" successful', $verbosity);
        } else {
            $verbosity = OutputInterface::VERBOSITY_NORMAL;
            static::$output->writeln('Assert: "' . trim($code) . '" failed', $verbosity);
        }

    }

    public static function printRequest($request)
    {
        $request['is_printed'] = true;
        $verbosity = OutputInterface::VERBOSITY_DEBUG;
        $request = Utils::unwrapValueContainer($request);
        static::$output->writeln('Request: "' . $request['method'] . '" failed', $verbosity);
        var_dump($request);

    }

    public static function printResponse($response)
    {
        $verbosity = OutputInterface::VERBOSITY_DEBUG;
        $response = Utils::unwrapValueContainer($response);
        if (!$response['network']) {
            static::$output->writeln('Network error', $verbosity);
            return;
        }

        static::$output->writeln('Response: ' . $response['status'], $verbosity);
        if (!empty($response['headers'])) {
            static::$output->writeln('Headers:', $verbosity);
            foreach ($response['headers'] as $name => $value) {
                static::$output->writeln('    ' . $name . ': ' . $value);
            }
        }
        if (!empty($response['cookies'])) {
            static::$output->writeln('Cookies:', $verbosity);
            foreach ($response['cookies'] as $name => $value) {
                static::$output->writeln('    ' . $name . ': ' . $value);
            }
        }
        if (!empty($response['raw'])) {
            static::$output->writeln('Body:', $verbosity);
            static::$output->writeln($response['raw'], $verbosity);
        }
        static::$output->writeln('', $verbosity);
    }


    public static function printMustExit(ContextStack $contextStack)
    {
        $verbosity = OutputInterface::VERBOSITY_DEBUG;
        if ($contextStack->head() instanceof GlobalContext) {
            static::$output->writeln('Testing is terminated', $verbosity);
        } elseif ($contextStack->head() instanceof TestcaseContext) {
            static::$output->writeln('Rest of test case "' . $contextStack->head()->getName() . '" is skipped', $verbosity);
        } elseif ($contextStack->head() instanceof EndpointContext) {
            static::$output->writeln('Rest of test case "' . $contextStack->neck()->getName() . '" is skipped', $verbosity);
        } else {
            static::$output->writeln('Command Must was called in inappropriate context', OutputInterface::VERBOSITY_NORMAL);
        }
    }


}