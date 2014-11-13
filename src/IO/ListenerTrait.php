<?php

namespace Tpavlek\PrintJobs\IO;

use League\Event\AbstractEvent;

trait ListenerTrait {
    /**
     * Checks the parameter passed to the handle function to ensure correctness
     *
     * A correct parameter is an instance of a printer
     *
     * @param $param
     * @param AbstractEvent $event
     * @param string $expected The class name we expect the parameter to be
     * @throws \Exception
     */
    private function checkParam($param, AbstractEvent $event, $expected) {
        if ($param === null) {
            $name = $event->getName();
            throw new \Exception("Expected a {$expected} but parameter is null on event: {$name}");
        }
    }
}
