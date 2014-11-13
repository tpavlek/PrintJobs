<?php

namespace Tpavlek\PrintJobs\IO;

use Illuminate\Database\Capsule\Manager;
use League\Event\AbstractEvent;
use League\Event\AbstractListener;
use Tpavlek\PrintJobs\IO\Events\NewJobEvent;
use Tpavlek\PrintJobs\IO\Events\NoJobsEvent;
use Tpavlek\PrintJobs\IO\Events\SendEmailEvent;
use Tpavlek\PrintJobs\IO\Events\StillStuckEvent;
use Tpavlek\PrintJobs\Job;
use Tpavlek\PrintJobs\Printer;

class Database extends AbstractListener {

    use ListenerTrait;

    /** @var Manager  */
    protected $capsule;

    public function __construct(Manager $capsule) {
        $this->capsule = $capsule->getConnection('default');
    }

    /**
     * Checks if the given printer is stuck.
     * @param $printer_name
     * @return boolean
     */
    public function isPrinterStuck($printer_name) {
        $latest_job = $this->capsule->table('jobs')
            ->where('printer_name', $printer_name)
            ->latest()
            ->get();

        return $latest_job['stuck'];
    }

    public function createJob(Job $job, $printer_name) {
        $this->capsule->table("jobs")
            ->insert([ "job_id" => $job->id, "printer_name" => $printer_name, "stuck" => false ]);
    }

    public function setStuck($printer_name, $stuck = false) {
        $this->capsule->table("jobs")
            ->where("printer_name", $printer_name)
            ->latest()
            ->update([ "stuck" => $stuck ]);
    }

    public function updateJob(Job $job, $printer_name, $stuck = true) {
        $query = $this->capsule->table("jobs")
            ->where('printer_name', $printer_name)
            ->where('job_id', $job->id);

        $query->update([ 'stuck' => $stuck ]);
    }

    /**
     * Handle an event.
     *
     * @param AbstractEvent $event
     *
     * @return void
     */
    public function handle(AbstractEvent $event, $param = null)
    {
        if ($event instanceof NewJobEvent) {
            $this->checkParam($param, $event, PrinterJob::class);
            /** @var PrinterJob $param */
            $this->createJob($param->job, $param->printer->name);
            return;
        }

        if ($event instanceof SendEmailEvent || $event instanceof StillStuckEvent) {
            $this->checkParam($param, $event, PrinterJob::class);
            /** @var PrinterJob $param */
            $this->setStuck($param->printer->name, true);
            return;
        }

        if ($event instanceof NoJobsEvent) {
            $this->checkParam($param, $event, Printer::class);
            /** @var Printer $param */
            $this->setStuck($param->name, false);
            return;
        }
    }
}
