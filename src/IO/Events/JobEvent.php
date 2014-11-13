<?php

namespace Tpavlek\PrintJobs\IO\Events;

use League\Event\AbstractEvent;
use Tpavlek\PrintJobs\IO\PrinterJob;

abstract class JobEvent extends AbstractEvent {

    public abstract function getMessage(PrinterJob $job);

} 
