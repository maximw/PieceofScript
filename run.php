#!/usr/bin/env php
<?php
require __DIR__.'/vendor/autoload.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$application = new Symfony\Component\Console\Application('PieceofScript', '0.0.1');
//$application->

/*
$command = new \PieceofScript\ListCommand();
$application->add($command);

$command = new \PieceofScript\HelpCommand();
$application->add($command);
*/
$command = new \PieceofScript\RunCommand();
$application->add($command);

$application->setDefaultCommand('list', false);
$application->run();