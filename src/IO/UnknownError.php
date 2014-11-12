<?php

namespace Tpavlek\PrintJobs\IO;

use Tpavlek\PrintJobs\Printer;

class UnknownError
{

    public $exception;
    public $printer;

    public function __construct(\Exception $exception, Printer $printer)
    {
        $this->exception = $exception;
        $this->printer = $printer;
    }

} 
