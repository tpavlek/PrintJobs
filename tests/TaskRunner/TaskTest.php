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

        $task = new \Tpavlek\PrintJobs\TaskRunner\Task($printer, $this->mock_io, $emitter);

        $this->assertAttributeEquals($printer, "printer", $task);
        $this->assertAttributeEquals($this->mock_io, "io", $task);
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

        $task = new \Tpavlek\PrintJobs\TaskRunner\Task($printer, $this->mock_io, $emitter);
        $task->run(null, null);
    }


}
