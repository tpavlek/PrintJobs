<?php

namespace Tpavlek\PrintJobs;

use Goutte\Client;

class PrinterFactory {

    /** @var Client  */
    protected $client;

    public function __construct(Client $client) {
        $this->client = $client;
    }

    public function make(array $printer_data) {
        $printer = new Printer($printer_data['url'] . $printer_data['path'], $printer_data['name'], $this->client);
        return $printer;
    }

} 
