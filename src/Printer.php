<?php

namespace Tpavlek\PrintJobs;

class Printer {

    const REMOTE_TABLE_NAME = ".tableDiv";

    protected $client;
    protected $url;

    public function __construct($url, \Goutte\Client $client) {
        $guzzle = $client->getClient();
        $guzzle->setDefaultOption('verify', false);
        $client->setClient($guzzle);

        $this->client = $client;
        $this->url = $url;
    }

    public function getFirstRemoteJob() {
        $crawler = $this->client->request('GET', $this->url);
        $first_job = $crawler->filter(self::REMOTE_TABLE_NAME . "> tbody tr")->first();
        return Job::parseFromDom($first_job);
    }

} 
