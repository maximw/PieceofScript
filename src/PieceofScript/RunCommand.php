<?php

namespace PieceofScript;

use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Errors\InternalError;
use PieceofScript\Services\Out\In;
use PieceofScript\Services\Out\Out;
use PieceofScript\Services\Parsing\CallLexer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PieceofScript\Services\Tester;


class RunCommand extends Command
{

    protected function configure()
    {
        $this->setName('run')
            ->setDescription('Run testing scenario')
            ->setHelp('This command runs testing scenario')
            ->addArgument('scenario', InputArgument::REQUIRED, 'Start script file')
            ->addOption('junit-report', 'j', InputOption::VALUE_OPTIONAL, 'Reporting file in JUnit format', null)
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Configuration file', null)
            ->addOption('local-storage', 'l', InputOption::VALUE_OPTIONAL, 'Local storage file', null)
            ->setHelp('Run testing scenario');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $lexer = new CallLexer();
        $call1 = $lexer->getCall('Hi there; {$x+5} $x1.2.gfhg.0 {{$page=$page;$test=$test}} // 23123213');
        $call2 = $lexer->getCall('  Hi {{$page=$page;$test\'}}\'}}there; $t {5}');

        $t = $call1->isEqual($call2);

        try {
            Out::setOutput($output);
            In::init($input, $output, $this->getHelper('question'));
            $startFile = realpath($input->getArgument('scenario'));
            if (!file_exists($startFile) || !is_readable($startFile)) {
                throw new InternalError('File is not readable ' . $input->getArgument('scenario'));
            }
            chdir(dirname($startFile));
            if ($input->getOption('config')) {
                Config::loadFromFile($input->getOption('config'), true);
            } else {
                Config::loadFromFile('./config.yaml', false);
            }
            Config::loadInput($input);
            $tester = new Tester($startFile, $input->getOption('junit-report'));
            return $tester->run();
        } catch (InternalError $e) {
            Out::printError($e);
            return 1;
        }
    }
}