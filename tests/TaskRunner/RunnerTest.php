<?php

class RunnerTest extends PHPUnit_Framework_TestCase {

    /** @var  Mockery\MockInterface */
    protected $mock_logger;

    protected $mock_io;

    public function setUp() {
        $this->mock_logger = Mockery::mock('Monolog\Logger');
        $this->mock_io = Mockery::mock('\Tpavlek\PrintJobs\IO\IO');
    }

    public function tearDown() {
        Mockery::close();
    }

    public function test_it_sets_class_variables() {
        $printer = [
            'name' => "mock_printer",
            'url' => "mock_url",
            'path' => "mock_path",
        ];

        $runner = new \Tpavlek\PrintJobs\TaskRunner\Runner(
            [ $printer ],
            new \Tpavlek\PrintJobs\TaskRunner\TaskFactory(new \Goutte\Client(), $this->mock_io),
            new \Tpavlek\PrintJobs\IO\IO($this->mock_logger, new \Tpavlek\PrintJobs\IO\Echoer())
        );

        $this->assertAttributeInternalType("array", "printers", $runner);
        $this->assertAttributeCount(1, "printers", $runner);
        $this->assertAttributeContains($printer, "printers", $runner);
    }

    public function test_it_runs_all_jobs() {
        $printers = [
            [ 'name' => "mock_printer", 'url' => "mock_url", "path" => "mock_path" ],
            [ 'name' => "mock_printer2", 'url' => "mock_url2", "path" => "mock_path2" ],
        ];

        $mock_factory = Mockery::mock('\Tpavlek\PrintJobs\TaskRunner\TaskFactory');
        $runner = new \Tpavlek\PrintJobs\TaskRunner\Runner(
            $printers,
            $mock_factory,
            new \Tpavlek\PrintJobs\IO\IO($this->mock_logger, new \Tpavlek\PrintJobs\IO\Echoer())
        );
        $mock_task = Mockery::mock('\Tpavlek\PrintJobs\TaskRunner\Task');
        $mock_factory->shouldReceive('make')->twice()->andReturn($mock_task);
        $mock_task->shouldReceive('run')->twice();

        $runner->run();
    }

}
