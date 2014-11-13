<?php

class TaskTest extends PHPUnit_Framework_TestCase
{

    /** @var  \Mockery\MockInterface */
    protected $mock_io;

    public function setUp()
    {
        $this->mock_io = $io = new \Tpavlek\PrintJobs\IO\IO(
            Mockery::mock('\Monolog\Logger'),
            new \Tpavlek\PrintJobs\IO\Echoer()
        );

    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_it_sets_class_variables()
    {
        $printer = new \Tpavlek\PrintJobs\Printer("mock_url", "mock_name", new \Goutte\Client());
        $emitter = new \League\Event\Emitter();

        $task = new \Tpavlek\PrintJobs\TaskRunner\Task($printer, $emitter);

        $this->assertAttributeEquals($printer, "printer", $task);
        $this->assertAttributeEquals($emitter, "emitter", $task);
    }

    public function test_it_emits_event_when_no_jobs_found()
    {
        $emitter = Mockery::mock(\League\Event\Emitter::class);
        $printer = Mockery::mock(\Tpavlek\PrintJobs\Printer::class);

        $printer->shouldReceive('getFirstRemoteJob')->once()->andReturnNull();

        // We expect to emit a NoJobsEvent while passing along the printer
        $emitter
            ->shouldReceive('emit')
            ->withArgs([ \Mockery::type(\Tpavlek\PrintJobs\IO\Events\NoJobsEvent::class), $printer ])
            ->once();

        $task = new \Tpavlek\PrintJobs\TaskRunner\Task($printer, $emitter);
        $task->run(null, null);
    }

    public function test_it_emits_event_when_email_already_sent() {
        $emitter = Mockery::mock(\League\Event\Emitter::class);
        $printer = Mockery::mock(\Tpavlek\PrintJobs\Printer::class);
        $job = new \Tpavlek\PrintJobs\Job(1, "mock_name", "mock_owner", "mock_status", "mock_type", 1);

        $printer->shouldReceive('getFirstRemoteJob')->once()->andReturn($job);

        $printer
            ->shouldReceive('loadLastJob')
            ->once()
            ->andReturn(new \Tpavlek\PrintJobs\JobData([
                'hash' => $job->hash(),
                'email_sent' => true,
                'date' => (string)\Carbon\Carbon::now()
            ]));
        // We expect to emit a NoJobsEvent while passing along the printer
        $emitter
            ->shouldReceive('emit')
            ->withArgs([
                \Mockery::type(\Tpavlek\PrintJobs\IO\Events\StillStuckEvent::class),
                \Mockery::type(\Tpavlek\PrintJobs\IO\PrinterJob::class)
            ])
            ->once();

        $task = new \Tpavlek\PrintJobs\TaskRunner\Task($printer, $emitter);
        $task->run(null, null);
    }

    public function test_it_saves_current_job_to_disk_if_job_is_new() {
        $printer = Mockery::mock(\Tpavlek\PrintJobs\Printer::class);
        $job = new \Tpavlek\PrintJobs\Job(1, "mock_name", "mock_owner", "mock_status", "mock_type", 1);

        $printer->shouldReceive('getFirstRemoteJob')->once()->andReturn($job);

        $printer->shouldReceive('loadLastJob')
            ->once()
            ->andReturn(new \Tpavlek\PrintJobs\JobData([
                'hash' => 'different_hash',
                'email_sent' => false,
                'date' => (string)\Carbon\Carbon::now()
            ]));

        $printer->shouldReceive('saveCurrentJob')
            ->once()
            ->withArgs([ $job, false ]);

        $task = new \Tpavlek\PrintJobs\TaskRunner\Task($printer, new \League\Event\Emitter());
        $task->run(null, null);
    }


}
