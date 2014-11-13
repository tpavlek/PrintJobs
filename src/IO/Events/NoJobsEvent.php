<?php

namespace Tpavlek\PrintJobs\IO\Events;

use League\Event\AbstractEvent;
use Tpavlek\PrintJobs\Printer;

class NoJobsEvent extends PrinterEvent
{

    function getMessage(Printer $printer)
    {
        return "No current jobs on printer: {$printer->name}";
    }
}
