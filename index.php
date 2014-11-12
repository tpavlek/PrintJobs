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
$emitter->addListener(\Tpavlek\PrintJobs\IO\Events\NoJobsEvent::class, $io);

foreach ($printjobs_config['printer_urls'] as $printer_url) {
    $printer_name = \Tpavlek\PrintJobs\Printer::getNameFromUrl($printer_url);
    $printer = new \Tpavlek\PrintJobs\Printer(
        $printer_url . $printjobs_config['jobs_path'],
        $printer_name,
        $container->get('client')
    );

    try {
        $task = new \Tpavlek\PrintJobs\TaskRunner\Task($printer, $io, $emitter);
        $task->run($printjobs_config, $container);
    } catch (GuzzleHttp\Exception\AdapterException $exception) {
        $io->error("-- \n Timed out connecting to printer: {$printer->name}\n");
    } catch (\Exception $exception) {
        $io->error("The following error occurred: '" . $exception->getMessage() . "' on printer {$printer->name}\n");
    }
}


