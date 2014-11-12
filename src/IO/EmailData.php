<?php

namespace Tpavlek\PrintJobs\IO;

use Tpavlek\PrintJobs\Job;
use Tpavlek\PrintJobs\Printer;

class EmailData {

    public $job;
    public $printer;

    public function __construct(Job $job, Printer $printer) {
        $this->job = $job;
        $this->printer = $printer;
    }

} 
