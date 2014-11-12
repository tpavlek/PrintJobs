<?php

namespace Tpavlek\PrintJobs\IO\Events;

use League\Event\AbstractEvent;
use Tpavlek\PrintJobs\Printer;

abstract class PrinterEvent extends AbstractEvent {

    abstract function getMessage(Printer $printer);

} 
