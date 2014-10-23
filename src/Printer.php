<?php

namespace Tpavlek\PrintJobs;

use Carbon\Carbon;

class Printer {

    const REMOTE_TABLE_NAME = ".tableDiv";
    const MAX_STALL_TIME = 180;

    protected $client;
    protected $url;
    protected $name;

    public function __construct($url, $printer_name, \Goutte\Client $client) {
        $guzzle = $client->getClient();
        $guzzle->setDefaultOption('verify', false);
        $client->setClient($guzzle);

        $this->name = $printer_name;
        $this->client = $client;
        $this->url = $url;
    }

    public function getFirstRemoteJob() {
        $crawler = $this->client->request('GET', $this->url);
        $first_job = $crawler->filter(self::REMOTE_TABLE_NAME . "> tbody tr")->first();
        return Job::parseFromDom($first_job);
    }

    public function getFilesystem() {
        return new PrinterFile($this->name);
    }
} 
