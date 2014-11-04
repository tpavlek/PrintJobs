<?php

class IOTest extends PHPUnit_Framework_TestCase {

    /**
     * @var \Mockery\MockInterface
     */
    protected $mock_logger;

    public function setUp() {
        $this->mock_logger = Mockery::mock(\Monolog\Logger::class);
    }

    public function tearDown() {
        Mockery::close();
    }

    public function testConstructor() {
        $io = new \Tpavlek\PrintJobs\IO($this->mock_logger);
        $this->assertAttributeEquals($this->mock_logger, "logger", $io);
    }

    public function testMessage() {
        $io = new \Tpavlek\PrintJobs\IO($this->mock_logger);

        $this->mock_logger->shouldReceive('addInfo')->with('mock_message')->once();
        $io->message("mock_message");
    }

    public function testError() {
        $io = new \Tpavlek\PrintJobs\IO($this->mock_logger);

        $this->mock_logger->shouldReceive('addError')->with('mock_message')->once();
        $io->error("mock_message");
    }

}
