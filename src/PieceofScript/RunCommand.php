<?php

namespace PieceofScript;

use PieceofScript\Services\Config\Config;
use PieceofScript\Services\Errors\InternalError;
use PieceofScript\Services\Out\Out;
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
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Configuration file', './config.yaml')
            ->setHelp('Run testing scenario');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            Out::setOutput($output);
            $startFile = realpath($input->getArgument('scenario'));
            if (!file_exists($startFile) || !is_readable($startFile)) {
                throw new InternalError('File is not readable ' . $input->getArgument('scenario'));
            }
            chdir(dirname($startFile));
            Config::loadFromFile($input->getOption('config'));
            $tester = new Tester($startFile, $output);
            return $tester->run();
        } catch (InternalError $e) {
            Out::printError($e);
            return 1;
        }
    }
}