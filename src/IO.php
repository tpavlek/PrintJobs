<?php

namespace Tpavlek\PrintJobs;

use Monolog\Logger;

/**
 * Class IO
 *
 * Abstracts all io operations away from other classes. Performs console output and logging.
 * @package Tpavlek\PrintJobs
 */
class IO {

    /**
     * A Monolog instance to handle outputing IO to logs.
     * @var Logger
     */
    protected $logger;

    /**
     * Construct an new IO instance.
     * @param Logger $logger
     */
    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * Outputs a new message.
     * @param $string
     */
    public function message($string) {
        $this->logger->addInfo($string);
        echo $string;
    }

    /**
     * Outputs a new error.
     * @param $string
     */
    public function error($string) {
        $this->logger->addError($string);
        echo "[ERROR] " . $string;
    }

} 
