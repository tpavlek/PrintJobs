<?php

namespace Tpavlek\PrintJobs\TaskRunner;

use League\Event\Emitter;
use Tpavlek\PrintJobs\IO\IO;
use Tpavlek\PrintJobs\Printer;

class Task
{

    /** @var Printer */
    protected $printer;
    /** @var \Tpavlek\PrintJobs\IO\IO */
    protected $io;
    /** @var Emitter  */
    protected $emitter;

    public function __construct(Printer $printer, IO $io, Emitter $emitter)
    {
        $this->printer = $printer;
        $this->io = $io;
        $this->emitter = $emitter;
    }

    public function run($printjobs_config, $container)
    {
        $job = $this->printer->getFirstRemoteJob();

        // If there are no jobs on the printer, then we can emit an event to clear jobs on that printer
        if (is_null($job)) {
            $this->emitter->emit(new \Tpavlek\PrintJobs\IO\Events\NoJobsEvent(), $this->printer);
            return;
        }

        $job_data = $this->printer->getFilesystem()->load();

        if (isset($job_data['email_sent']) && $job_data['email_sent'] === true) {
            // We've already dealt with this one, let's move on.
            $this->io->message("Job {$job->id} is still stuck on printer: {$this->printer->name} - Fix it!\n");
            return;
        }

        // If the job hash in the file matches our current job hash, then the job has stayed between runs. Take action.
        if (isset($job_data['hash']) && $job_data['hash'] == $job->hash()) {
            // Check if the job has been stuck for longer than the allowable time.
            $file_time = new \Carbon\Carbon($job_data['date']);
            $current_time = \Carbon\Carbon::now();

            if ($current_time->diffInSeconds($file_time, true) > $printjobs_config['max_stall_time']) {
                $email = new \Tpavlek\PrintJobs\Email($job, $this->printer, $container->get('mailer'),
                    $container->get('message'));
                $email->send($printjobs_config['send_to']);
                $this->io->message("Sending email about job {$job->id} on printer {$this->printer->name}\n");
                // Save email_sent => true to the file so we don't send duplicate emails about this job.
                $this->printer->getFilesystem()->save($job, true);
            }

        } else {
            // We don't have the same hash, or the file is empty, so lets drop the current job there.
            $this->printer->getFilesystem()->save($job, false);
        }

        $this->io->message("Job {$job->id} is running on printer {$this->printer->name}\n");
    }
} 
