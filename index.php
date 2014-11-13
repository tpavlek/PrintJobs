<?php
use League\Event\Emitter;
use Tpavlek\PrintJobs\IO\Email;
use Tpavlek\PrintJobs\IO\Events\NoJobsEvent;
use Tpavlek\PrintJobs\IO\Events\SendEmailEvent;
use Tpavlek\PrintJobs\IO\Events\StillRunningEvent;
use Tpavlek\PrintJobs\IO\Events\StillStuckEvent;
use Tpavlek\PrintJobs\IO\Events\TimedOutEvent;
use Tpavlek\PrintJobs\IO\Events\UnknownErrorEvent;
use Tpavlek\PrintJobs\IO\IO;

require "bootstrap/start.php";

/**
 * Handles the IO to the log file/console.
 * @var IO $io
 */
$io = $container->get('io');
/** @var Emitter $emitter */
$emitter = $container->get('emitter');
/** @var Email $email */
$email = $container->get('email');

// Register our IO listeners
$emitter->addListener(NoJobsEvent::class, $io);
$emitter->addListener(SendEmailEvent::class, $io);
$emitter->addListener(StillRunningEvent::class, $io);
$emitter->addListener(StillStuckEvent::class, $io);
$emitter->addListener(TimedOutEvent::class, $io);
$emitter->addListener(UnknownErrorEvent::class, $io);

// Register our email listener
$emitter->addListener(SendEmailEvent::class, $email);


$runner = new \Tpavlek\PrintJobs\TaskRunner\Runner(
    $printjobs_config['printers'],
    $container->get('printerFactory'),
    $container->get('taskFactory'),
    $emitter
);

$runner->run($printjobs_config, $container);

