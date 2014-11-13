<?php

namespace Tpavlek\PrintJobs\IO\Events;

use Tpavlek\PrintJobs\IO\PrinterJob;

class NewJobEvent extends JobEvent {

    public function getMessage(PrinterJob $job)
    {
        return "New job {$job->job->id} on printer {$job->printer->name}";
    }
}
