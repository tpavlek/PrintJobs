<?php
// The querystring that is concatenated to the end of the printer URL to view the current jobs.
// Can be overridden on a per printer basis in the printers array below.
$default_jobs_path = "jobs/active.php?tab=jobs";

$printjobs_config = [

    /*
     * List of printers.
     * Each printer takes the form of an array taking a printer name, URL to the web server, and path
     * on that webserver to the jobs page.
     */
    'printers' => [
        [
            'name' => "Printer 1",
            'url' => "https://129.128.183.8/",
            'path' => $default_jobs_path,
        ],
    ],

    // Web addresses of all the printers we want to check on
    'printer_urls' => [
        "https://129.128.183.8/",
        "https://129.128.183.21/",
        "https://129.128.183.22/",
        "https://129.128.183.51/"
    ],



    // An array of email addresses that should recieve notification if a printer is down.
    'send_to' => [
        "tpavlek@ualberta.ca",
    ],

    // Time, in seconds, that a print job can remain at the top of the queue before an email is sent
    'max_stall_time' => 180,
];
