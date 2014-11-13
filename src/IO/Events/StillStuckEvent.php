<?php

namespace Tpavlek\PrintJobs\IO\Events;

use Tpavlek\PrintJobs\IO\PrinterJob;

class StillStuckEvent extends JobEvent {

    public function getMessage(PrinterJob $job)
    {
        return "Job {$job->job->id} is still stuck on printer: {$job->printer->name} - Fix it!";
    }
}
