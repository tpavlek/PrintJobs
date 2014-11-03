Xerox PrintJobs Checker
========================

[![Build Status](https://travis-ci.org/tpavlek/PrintJobs.svg?branch=master)](https://travis-ci.org/tpavlek/PrintJobs)
[![Coverage Status](https://coveralls.io/repos/tpavlek/PrintJobs/badge.png?branch=master)](https://coveralls.io/r/tpavlek/PrintJobs?branch=master)

This is a script that checks the status of the jobs on Xerox printers with the web interface enabled, and will email an
employee to notify about stuck jobs.

Installation
-------------

You will need to have [Composer](https://getcomposer.org/) installed.

Clone the repository to a directory and run 

```
composer install
```

This will download all dependencies and set up the environment.

If you want to change any configuration options, you can edit them in `config/config.php`.

Using the Checker
------------------

The checker runs and saves its state on each run. It is meant to be polling on a set interval around ~1 minute a part.

Recurring jobs are not in the scope of this program, simply set up the script with an automated task runner like `cron`
and have that automatically run the script for you.

To run the script use `php index.php`. This will run the script, and output the results to both a log and the console.

Notes
------

This application uses unauthenticated SMTP to send emails, so it must be run on a machine on University of Alberta
networks.
