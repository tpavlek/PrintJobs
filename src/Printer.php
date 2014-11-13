<?php

namespace Tpavlek\PrintJobs;

use Carbon\Carbon;
use League\Url\Url;

class Printer {

    const REMOTE_TABLE_NAME = ".tableDiv";

    public $client;
    public $url;
    public $name;

    /**
     * Construct a new Printer.
     *
     * @param $url
     * @param $printer_name
     * @param \Goutte\Client $client
     */
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
     * Loads the last job off disk.
     *
     * @return JobData
     */
    public function loadLastJob() {
        return $this->getFilesystem()->load();
    }

    public function saveCurrentJob(Job $job, $email_sent = false) {
        $this->getFilesystem()->save($job, $email_sent);
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

    /**
     * Get the link to the management URL for this printer.
     *
     * @return string
     */
    public function getManagementUrl() {
        $url = Url::createFromUrl($this->url);
        return $url->getBaseUrl() . "/properties/authentication/login.php?redir=/support/remoteUI/RUIViewer.php?login=true";
    }

    /**
     * Gets the printer name from its URL.
     *
     * The printer name is the last digit in the IP address.
     *
     * @param string $url
     * @return string
     */
    public static function getNameFromUrl($url) {
        return explode('/', explode('.', $url)[3])[0];
    }
} 
