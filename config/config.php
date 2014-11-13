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
            'name' => "2nd_Floor_Xerox_5790",
            'url' => "https://129.128.183.8/",
            'path' => $default_jobs_path,
        ],
        [
            'name' => "3rd_Floor_B_Xerox_5790",
            'url' => "https://129.128.183.21/",
            'path' => $default_jobs_path,
        ],
        [
            'name' => "3rd_Floor_A_Xerox_5790",
            'url' => "https://129.128.183.22/",
            'path' => $default_jobs_path,
        ],
        [
            'name' => "4th_Floor_Xerox_5790",
            'url' => "https://129.128.183.51/",
            'path' => $default_jobs_path,
        ],
        [
            'name' => "Dean_Xerox_7545",
            'url' => "https://129.128.183.56/",
            'path' => $default_jobs_path,
        ],
        [
            'name' => "EE ESQ Xerox_5790",
            'url' => "https://142.244.15.5/",
            'path' => $default_jobs_path,
        ],
        [
            'name' => "EMBA_Stollery_Xerox_5735",
            'url' => "https://129.128.183.33/",
            'path' => $default_jobs_path,
        ],
    ],

    // An array of email addresses that should recieve notification if a printer is down.
    'send_to' => [
        "tpavlek@ualberta.ca",
    ],

    // Time, in seconds, that a print job can remain at the top of the queue before an email is sent
    'max_stall_time' => 180,

    /*
     * Database configuration. This pulls from the environment that the software is running as. So to set one of
     * these variables in bash you would run `export PRINTJOBS_DATABASE="my_db_name"`
     *
     * This is passed to an Eloquent capsule manager instance, so any configuration options supported by eloquent
     * are supported here.
     */
    'db' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'database' => getenv('PRINTJOBS_DATABASE'),
        'username' => getenv('PRINTJOBS_DATABASE_USER'),
        'password' => getenv('PRINTJOBS_DATABASE_PASSWORD'),
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => ''
    ],
];
