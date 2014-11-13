<?php

namespace Tpavlek\PrintJobs;

use Carbon\Carbon;
use Tpavlek\PrintJobs\Exceptions\InvalidJobDataException;
use Tpavlek\PrintJobs\IO\ListenerTrait;

class PrinterFile
{

    use ListenerTrait;

    protected $filename;
    protected $path;

    public function __construct($printer_name, $path = "")
    {
        $this->filename = "{$path}printer-{$printer_name}.json";
        $this->createFileIfNotExists();
    }

    private function createFileIfNotExists()
    {
        if (!file_exists($this->filename)) {
            file_put_contents($this->filename, "{}");
        }
    }

    public function clear()
    {
        file_put_contents($this->filename, "{}");
    }

    /**
     * Loads the job data from a file on disk.
     *
     * @return JobData
     * @throws InvalidJobDataException
     */
    public function load()
    {
        $job = new JobData(json_decode(file_get_contents($this->filename), true));
        return $job;
    }

    public function save(Job $job, $email_sent = false)
    {
        $job_data = [
            'hash' => $job->hash(),
            'date' => (string)Carbon::now(),
            'email_sent' => $email_sent
        ];
        file_put_contents($this->filename, json_encode($job_data));
    }
}
