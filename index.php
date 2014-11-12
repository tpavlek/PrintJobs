<?php
use League\Event\Emitter;
use Tpavlek\PrintJobs\IO\IO;

require "bootstrap/start.php";

/**
 * Handles the IO to the log file/console.
 * @var IO $io
 */
$io = $container->get('io');
/** @var Emitter $emitter */
$emitter = $container->get('emitter');

// Register our listeners
$emitter->addListener(\Tpavlek\PrintJobs\IO\Events\NoJobsEvent::class, $io);

$runner = new \Tpavlek\PrintJobs\TaskRunner\Runner(
    $printjobs_config['printers'],
    $container->get('printerFactory'),
    $container->get('taskFactory'),
    $emitter
);

$runner->run($printjobs_config, $container);

