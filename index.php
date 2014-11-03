<?php
use Tpavlek\PrintJobs\IO;

require "bootstrap/start.php";

/**
 * Handles the IO to the log file/console.
 * @var IO $io
 */
$io = $container->get('io');

foreach ($printjobs_config['printer_urls'] as $printer_url) {
    $printer_name = \Tpavlek\PrintJobs\Printer::getNameFromUrl($printer_url);
    $printer = new \Tpavlek\PrintJobs\Printer($printer_url . $printjobs_config['jobs_path'], $printer_name, $container->get('client'));

    try {
        $job = $printer->getFirstRemoteJob();

        // If there are no current jobs, we can clear the current job hash for that printer
        if (is_null($job)) {
            $printer->getFilesystem()->clear();
            $io->message("No current jobs on printer: {$printer_name}\n");
            continue;
        }

        $job_data = $printer->getFilesystem()->load();

        if ( isset($job_data['email_sent']) && $job_data['email_sent'] === true) {
            // We've already dealt with this one, let's move on.
            $io->message("Job is still stuck on printer: {$printer_name} - Fix it!\n");
            continue;
        }

        // If the job hash in the file matches our current job hash, then the job has stayed between runs. Take action.
        if (isset($job_data['hash']) && $job_data['hash'] == $job->hash()) {
            // Check if the job has been stuck for longer than the allowable time.
            $file_time = new \Carbon\Carbon($job_data['date']);
            $current_time = \Carbon\Carbon::now();

            if ($current_time->diffInSeconds($file_time) > $printjobs_config['max_stall_time']) {
                $email = new \Tpavlek\PrintJobs\Email($job, $printer, $container->get('mailer'), $container->get('message'));
                $email->send($printjobs_config['send_to']);
                // Save email_sent => true to the file so we don't send duplicate emails about this job.
                $printer->getFilesystem()->save($job, true);
            }

        } else {
            // We don't have the same hash, or the file is empty, so lets drop the current job there.
            $printer->getFilesystem()->save($job, false);
        }

        $io->message("Job {$job->id} is running on printer {$printer->name}\n");

    } catch (GuzzleHttp\Exception\AdapterException $exception) {
        $io->error("-- \n Timed out connecting to printer: {$printer->name}\n");
    } catch (\Exception $exception) {
        $io->error($exception->getMessage() ."\n");
    }
}


