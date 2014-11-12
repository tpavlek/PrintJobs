<?php

namespace Tpavlek\PrintJobs\IO;

use League\Event\AbstractEvent;
use League\Event\AbstractListener;
use Monolog\Logger;
use Tpavlek\Printjobs\IO\Events\NoJobsEvent;
use Tpavlek\PrintJobs\IO\Events\PrinterEvent;
use Tpavlek\PrintJobs\IO\Events\UnknownErrorEvent;
use Tpavlek\PrintJobs\Printer;

/**
 * Class IO
 *
 * Abstracts all io operations away from other classes. Performs console output and logging.
 * @package Tpavlek\PrintJobs
 */
class IO extends AbstractListener
{

    /**
     * A Monolog instance to handle outputing IO to logs.
     * @var Logger
     */
    protected $logger;

    /**
     * A wrapper around PHP's echo function to allow for testability of this IO class.
     * @var Echoer
     */
    protected $echoer;

    /**
     * Construct an new IO instance.
     * @param Logger $logger
     * @param Echoer $echoer
     */
    public function __construct(Logger $logger, Echoer $echoer)
    {
        $this->logger = $logger;
        $this->echoer = $echoer;
    }

    /**
     * Outputs a new message.
     * @param $string
     */
    public function message($string)
    {
        $this->logger->addInfo($string);
        $this->echoer->write($string);
    }

    /**
     * Outputs a new error.
     * @param $string
     */
    public function error($string)
    {
        $this->logger->addError($string);
        $this->echoer->write("[ERROR] " . $string);
    }

    /**
     * Handle an event.
     *
     * @param AbstractEvent $event
     *
     * @param null $param
     * @throws \Exception
     * @return void
     */
    public function handle(AbstractEvent $event, $param = null)
    {
        if ($event instanceof NoJobsEvent) {
            $this->checkParam($param, $event, Printer::class);
            $this->message($event->getMessage($param));
            return;
        }

        // This must come after NoJobsEvent, as NoJobsEvent inherits from printer event
        // NoJobsEvent should simply log information, where all other PrinterEvents should be considered errors.
        if ($event instanceof PrinterEvent) {
            $this->checkParam($param, $event, Printer::class);
            $this->error($event->getMessage($param));
            return;
        }

        if ($event instanceof UnknownErrorEvent) {
            $this->checkParam($param, $event, UnknownError::class);
            $this->error($event->getMessage($param));
            return;
        }

    }

    /**
     * Checks the parameter passed to the handle function to ensure correctness
     *
     * A correct parameter is an instance of a printer
     *
     * @param $param
     * @param AbstractEvent $event
     * @param string $expected The class name we expect the parameter to be
     * @throws \Exception
     */
    private function checkParam($param, AbstractEvent $event, $expected) {
        if ($param === null) {
            $name = $event->getName();
            throw new \Exception("Expected a {$expected} but parameter is null on event: {$name}");
        }
    }
}
