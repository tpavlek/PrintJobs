<?php

namespace Tpavlek\PrintJobs\TaskRunner;

use Goutte\Client;
use League\Event\Emitter;
use Tpavlek\PrintJobs\IO\IO;
use Tpavlek\PrintJobs\Printer;

class TaskFactory {

    /** @var Client  */
    protected $client;

    public function __construct(Emitter $emitter) {
        $this->emitter = $emitter;
    }

    public function make(Printer $printer) {
        return new Task($printer, $this->emitter);
    }
} 
