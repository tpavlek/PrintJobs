<?php

namespace Tpavlek\PrintJobs\IO\Events;

use Tpavlek\PrintJobs\Printer;

class TimedOutEvent extends PrinterEvent {

    function getMessage(Printer $printer)
    {
        return "Timed out connecting to printer: {$printer->name}";
    }
}
