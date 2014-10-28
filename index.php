<?php

require 'vendor/autoload.php';

$jobs_path = "jobs/active.php?tab=jobs";

$printer_urls = [
    "https://129.128.183.8/",
    "https://129.128.183.21/",
    "https://129.128.183.22/",
    "https://129.128.183.51/"
];

$client = new Goutte\Client();

foreach ($printer_urls as $printer_url) {
    $printer_name = explode('/', explode('.', $printer_url)[3])[0];
    $printer = new \Tpavlek\PrintJobs\Printer($printer_url . $jobs_path, $printer_name, $client);
    $job = $printer->getFirstRemoteJob();

    // If there are no current jobs, we can clear the current job hash for that printer
    if (is_null($job)) {
        $printer->getFilesystem()->clear();
        echo "No current jobs on printer: {$printer_name}\n";
    } else {
        $job_data = $printer->getFilesystem()->load();
        if ( isset($job_data['email_sent']) && $job_data['email_sent'] === true) {
            // We've already dealt with this one, let's move on.
            echo "Job is still stuck on printer: {$printer_name} - Fix it!\n";
            continue;
        }

        if (isset($job_data['hash']) && $job_data['hash'] == $job->hash()) {
            // Check if the job has been stuck for longer than the allowable time.
            $file_time = new \Carbon\Carbon($job_data['date']);
            $current_time = \Carbon\Carbon::now();
            if ($current_time->diffInSeconds($file_time) > \Tpavlek\PrintJobs\Printer::MAX_STALL_TIME) {
                $mailer = new \Nette\Mail\SmtpMailer([
                    'host' => 'smtp.srv.ualberta.ca',
                    'username' => 'glados@ualberta.ca'
                ]);
                $email = new \Tpavlek\PrintJobs\Email($job, $printer, $mailer, new \Nette\Mail\Message());
                $email->send();
                $printer->getFilesystem()->save($job, true);
            }
        } else {
            // We don't have the same hash, or the file is empty, so lets drop the current job there.
            $printer->getFilesystem()->save($job, false);
        }
    }
}


