<?php

namespace Tpavlek\PrintJobs\IO\Events;

use League\Event\AbstractEvent;
use Tpavlek\PrintJobs\IO\UnknownError;

class UnknownErrorEvent extends AbstractEvent {

    public function getMessage(UnknownError $error) {
        return "The following error occurred: '" . $error->exception->getMessage() . "' on printer {$error->printer->name}";
    }

} 
