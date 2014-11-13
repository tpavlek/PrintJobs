<?php

namespace Tpavlek\PrintJobs;

use Carbon\Carbon;
use Tpavlek\PrintJobs\Exceptions\InvalidJobDataException;

/**
 * Class JobData
 *
 * Represents the JSON encoded information about a job that is serialized to disk.
 *
 * Different from jobs in a couple ways, chiefly that Jobs aren't aware if emails have been sent about them,
 * but JobData is.
 *
 * @package Tpavlek\PrintJobs\IO
 */
class JobData {

    /** @var  string */
    public $hash;
    /** @var  bool */
    public $email_sent;
    /** @var Carbon  */
    public $date;

    public function __construct(array $job_data) {
        $this->hash = (isset($job_data['hash'])) ? $job_data['hash'] : "";
        $this->email_sent = (isset($job_data['email_sent'])) ? $job_data['email_sent'] : false;
        $this->date = (isset($job_data['date'])) ? new Carbon($job_data['date']) : Carbon::now();
    }

    /**
     * Checks if the job has been stuck for more than the given number of seconds
     *
     * @param $seconds
     * @return bool
     */
    public function hasBeenStuckFor($seconds) {
        $current_time = Carbon::now();

        return ($current_time->diffInSeconds($this->date, true) > $seconds);
    }

} 
