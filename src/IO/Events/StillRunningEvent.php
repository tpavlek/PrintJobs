<?php

namespace Tpavlek\PrintJobs\IO\Events;

use Tpavlek\PrintJobs\IO\PrinterJob;

class StillRunningEvent extends JobEvent {

    public function getMessage(PrinterJob $job)
    {
        return "Job {$job->job->id} is running on printer {$job->printer->name}";
    }
}
