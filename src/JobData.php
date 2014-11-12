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
        if (!isset($job_data['hash']) || !isset($job_data['email_sent']) || !isset($job_data['date'])) {
            throw new InvalidJobDataException();
        }
        $this->hash = $job_data['hash'];
        $this->email_sent = $job_data['email_sent'];
        $this->date = new Carbon($job_data['date']);
    }

} 
