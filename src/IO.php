<?php

namespace Tpavlek\PrintJobs;

use Monolog\Logger;

class IO {

    protected $logger;

    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    public function message($string) {
        $this->logger->addInfo($string);
        echo $string;
    }

    public function error($string) {
        $this->logger->addError($string);
        echo "[ERROR] " . $string;
    }

} 
