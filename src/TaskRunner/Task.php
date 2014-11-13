<?php

namespace Tpavlek\PrintJobs\TaskRunner;

use Carbon\Carbon;
use League\Event\Emitter;
use Tpavlek\PrintJobs\IO\Events\NoJobsEvent;
use Tpavlek\PrintJobs\IO\Events\SendEmailEvent;
use Tpavlek\PrintJobs\IO\Events\StillRunningEvent;
use Tpavlek\PrintJobs\IO\Events\StillStuckEvent;
use Tpavlek\PrintJobs\IO\IO;
use Tpavlek\PrintJobs\IO\PrinterJob;
use Tpavlek\PrintJobs\Printer;

class Task
{

    /** @var Printer */
    protected $printer;
    /** @var Emitter  */
    protected $emitter;

    public function __construct(Printer $printer, Emitter $emitter)
    {
        $this->printer = $printer;
        $this->emitter = $emitter;
    }

    public function run($printjobs_config)
    {
        $job = $this->printer->getFirstRemoteJob();

        // If there are no jobs on the printer, then we can emit an event to clear jobs on that printer
        if (is_null($job)) {
            $this->emitter->emit(new NoJobsEvent(), $this->printer);
            return;
        }

        $job_data = $this->printer->loadLastJob();

        // If the job hash does not match then we'll sync the current job to disk.
        if ($job_data->hash !== $job->hash()) {
            $this->printer->saveCurrentJob($job, false);
            return;
        }

        // We don't want to send an email about the same job more than once.
        if ($job_data->email_sent) {
            $this->emitter->emit(new StillStuckEvent(), new PrinterJob($this->printer, $job));
            return;
        }

        if ($job_data->hasBeenStuckFor($printjobs_config['max_stall_time'])) {
            $this->emitter->emit(new SendEmailEvent(), new PrinterJob($this->printer, $job));
            return;
        }

        // If we've gotten here, the job is kosher and still running.
        $this->emitter->emit(new StillRunningEvent(), new PrinterJob($this->printer, $job));
    }
} 
