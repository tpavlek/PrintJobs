<?php

namespace Tpavlek\PrintJobs\TaskRunner;

use Goutte\Client;
use Tpavlek\PrintJobs\IO\IO;
use Tpavlek\PrintJobs\Printer;

class TaskFactory {

    /** @var Client  */
    protected $client;

    public function __construct(Client $client, IO $io) {
        $this->client = $client;
        $this->io = $io;
    }

    public function make(array $printer_data) {
        $printer = new Printer($printer_data['url'] . $printer_data['path'], $printer_data['name'], $this->client);
        return new Task($printer, $this->io);
    }
} 
