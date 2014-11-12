<?php

class IOTest extends PHPUnit_Framework_TestCase {

    /**
     * @var \Mockery\MockInterface
     */
    protected $mock_logger;

    /** @var  \Mockery\MockInterface */
    protected $mock_echo;

    public function setUp() {
        $this->mock_logger = Mockery::mock(\Monolog\Logger::class);
        $this->mock_echo = Mockery::mock(\Tpavlek\PrintJobs\IO\Echoer::class);
    }

    public function tearDown() {
        Mockery::close();
    }

    public function testConstructor() {
        $io = new \Tpavlek\PrintJobs\IO\IO($this->mock_logger, $this->mock_echo);
        $this->assertAttributeEquals($this->mock_logger, "logger", $io);
    }

    public function testMessage() {
        $io = new \Tpavlek\PrintJobs\IO\IO($this->mock_logger, $this->mock_echo);

        $this->mock_logger->shouldReceive('addInfo')->with('mock_message')->once();
        $this->mock_echo->shouldReceive('write')->with('mock_message')->once();
        $io->message("mock_message");
    }

    public function testError() {
        $io = new \Tpavlek\PrintJobs\IO\IO($this->mock_logger, $this->mock_echo);

        $this->mock_logger->shouldReceive('addError')->with('mock_message')->once();
        $this->mock_echo->shouldReceive('write')->with('[ERROR] mock_message')->once();
        $io->error("mock_message");
    }

    public function test_it_handles_no_job_events() {
        $io = new \Tpavlek\PrintJobs\IO\IO($this->mock_logger, $this->mock_echo);

        $event = new \Tpavlek\PrintJobs\IO\Events\NoJobsEvent();
        $printer = new \Tpavlek\PrintJobs\Printer("mock_url", "mock_printer", new \Goutte\Client());

        $expected_message = $event->getMessage($printer);

        $this->mock_logger->shouldReceive('addInfo')->with($expected_message)->once();
        $this->mock_echo->shouldReceive('write')->with($expected_message)->once();
        $io->handle($event, $printer);
    }

    public function test_it_handles_general_printer_events() {
        $io = new \Tpavlek\PrintJobs\IO\IO($this->mock_logger, $this->mock_echo);

        $event = new \Tpavlek\PrintJobs\IO\Events\TimedOutEvent();
        $printer = new \Tpavlek\PrintJobs\Printer("mock_url", "mock_printer", new \Goutte\Client());

        $expected_message = $event->getMessage($printer);
        $this->mock_logger->shouldReceive('addError')->with($expected_message)->once();
        $this->mock_echo->shouldReceive('write')->with("[ERROR] " . $expected_message)->once();

        $io->handle($event, $printer);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Expected a Tpavlek\PrintJobs\Printer but parameter is null on event: Tpavlek\PrintJobs\IO\Events\NoJobsEvent
     */
    public function test_it_handles_events_without_parameter_by_throwing() {
        $io = new \Tpavlek\PrintJobs\IO\IO($this->mock_logger, $this->mock_echo);

        $event = new \Tpavlek\PrintJobs\IO\Events\NoJobsEvent();
        $io->handle($event, null);
    }
}
