<?php

namespace Tpavlek\PrintJobs;

use Carbon\Carbon;

class Printer {

    const REMOTE_TABLE_NAME = ".tableDiv";
    const MAX_STALL_TIME = 180;

    public $client;
    public $url;
    public $name;

    public function __construct($url, $printer_name, \Goutte\Client $client) {
        $this->name = $printer_name;
        $this->client = $client;
        $this->url = $url;
    }

    /**
     * Gets the first remote job from the server.
     *
     * Returns null if no jobs exist on the printer.
     * @return null|Job
     */
    public function getFirstRemoteJob() {
        $crawler = $this->client->request('GET', $this->url);
        $first_job = $crawler->filter(self::REMOTE_TABLE_NAME . "> tbody tr")->first();
        return Job::parseFromDom($first_job);
    }

    /**
     * Get the filesystem representation of the printer.
     *
     * Printers last job are stored in JSON files on the filesystem.
     * @return PrinterFile
     */
    public function getFilesystem() {
        return new PrinterFile($this->name);
    }

    public static function getNameFromUrl($url) {
        return explode('/', explode('.', $url)[3])[0];
    }
} 
