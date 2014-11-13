<?php

namespace Tpavlek\PrintJobs\IO;

use Tpavlek\PrintJobs\Job;
use Tpavlek\PrintJobs\Printer;

/**
 * Class PrinterJob
 *
 * References a job in the context of its owning printer
 *
 * @package Tpavlek\PrintJobs\IO
 */
class PrinterJob {

    public $job;
    public $printer;

    public function __construct(Printer $printer, Job $job) {
        $this->job = $job;
        $this->printer = $printer;
    }

} 
