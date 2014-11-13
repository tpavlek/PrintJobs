<?php

namespace Tpavlek\PrintJobs\IO;

use League\Event\AbstractEvent;
use League\Event\AbstractListener;
use Symfony\Component\Filesystem\Filesystem;
use Tpavlek\PrintJobs\IO\Events\NewJobEvent;
use Tpavlek\PrintJobs\IO\Events\NoJobsEvent;
use Tpavlek\PrintJobs\IO\Events\SendEmailEvent;
use Tpavlek\PrintJobs\Printer;
use Tpavlek\PrintJobs\PrinterFile;

class PrinterFileService extends AbstractListener {

    use ListenerTrait;
    /**
     * Handle an event.
     *
     * @param AbstractEvent $event
     *
     * @param null $param
     * @throws \Exception
     * @return void
     */
    public function handle(AbstractEvent $event, $param = null)
    {
        if ($event instanceof NoJobsEvent) {
            $this->checkParam($param, $event, Printer::class);
            /** @var Printer $param */
            $filesystem = new PrinterFile($param->name);
            $filesystem->clear();
            return;
        }

        if ($event instanceof NewJobEvent) {
            $this->checkParam($param, $event, PrinterJob::class);
            /** @var PrinterJob $param */
            $filesystem = new PrinterFile($param->printer->name);
            $filesystem->save($param->job);
        }

        if ($event instanceof SendEmailEvent) {
            $this->checkParam($param, $event, PrinterJob::class);
            /** @var PrinterJob $param */
            $filesystem = new PrinterFile($param->printer->name);
            $filesystem->save($param->job, true);
        }
    }
}
