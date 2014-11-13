<?php

namespace Tpavlek\PrintJobs\TaskRunner;

use Goutte\Client;
use League\Event\Emitter;
use Tpavlek\PrintJobs\IO\Events\TimedOutEvent;
use Tpavlek\PrintJobs\IO\Events\UnknownErrorEvent;
use Tpavlek\PrintJobs\IO\IO;
use Tpavlek\PrintJobs\IO\UnknownError;
use Tpavlek\PrintJobs\Printer;
use Tpavlek\PrintJobs\PrinterFactory;

class Runner
{

    protected $printers;
    /** @var TaskFactory */
    protected $factory;
    protected $emitter;

    /**
     * Construct a new Runner instance.
     * @param array $printers An array of array representations of the printers to process.
     * @param PrinterFactory $printerFactory
     * @param TaskFactory $taskFactory
     * @param Emitter $emitter
     */
    public function __construct(array $printers, PrinterFactory $printerFactory, TaskFactory $taskFactory, Emitter $emitter)
    {
        $this->printers = $printers;
        $this->taskFactory = $taskFactory;
        $this->printerFactory = $printerFactory;
        $this->emitter = $emitter;
    }

    public function run($printjobs_config)
    {
        foreach ($this->printers as $printer_data) {
            $printer = $this->printerFactory->make($printer_data);

            try {
                $this->taskFactory->make($printer)->run($printjobs_config);
            } catch (\GuzzleHttp\Exception\AdapterException $exception) {
                $this->emitter->emit(new TimedOutEvent(), $printer);
            } catch (\Exception $exception) {
                $this->emitter->emit(new UnknownErrorEvent(), new UnknownError($exception, $printer));
            }
        }
    }

} 
