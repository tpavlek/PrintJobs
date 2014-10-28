<?php

namespace Tpavlek\PrintJobs;

use Carbon\Carbon;

class PrinterFile {

    protected $filename;
    protected $path;

    public function __construct($printer_name, $path = "") {
        $this->filename = "{$path}printer-{$printer_name}.json";
        $this->createFileIfNotExists();
    }

    private function createFileIfNotExists() {
        if (! file_exists($this->filename)) {
            file_put_contents($this->filename, "{}");
        }
    }

    public function clear() {
        file_put_contents($this->filename, "{}");
    }

    public function load() {
        $job = (array)json_decode(file_get_contents($this->filename));
        return $job;
    }

    public function save(Job $job, $email_sent = false) {
        $job_data = [
            'hash' => $job->hash(),
            'date' => (string)Carbon::now(),
            'email_sent' => $email_sent
        ];
        file_put_contents($this->filename, json_encode($job_data));
    }
} 
