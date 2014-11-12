<?php

use Tpavlek\PrintJobs\IO\UnknownError;

class RunnerTest extends PHPUnit_Framework_TestCase
{

    /** @var  Mockery\MockInterface */
    protected $mock_logger;

    protected $mock_io;

    public function setUp()
    {
        $this->mock_logger = Mockery::mock(Monolog\Logger::class);
        $this->mock_io = Mockery::mock(\Tpavlek\PrintJobs\IO\IO::class);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_it_sets_class_variables()
    {
        $printer = [
            'name' => "mock_printer",
            'url' => "mock_url",
            'path' => "mock_path",
        ];

        $runner = new \Tpavlek\PrintJobs\TaskRunner\Runner(
            [$printer],
            new \Tpavlek\PrintJobs\PrinterFactory(new Goutte\Client()),
            new \Tpavlek\PrintJobs\TaskRunner\TaskFactory(
                new \Goutte\Client(),
                $this->mock_io,
                new League\Event\Emitter()
            ),
            new \League\Event\Emitter()
        );

        $this->assertAttributeInternalType("array", "printers", $runner);
        $this->assertAttributeCount(1, "printers", $runner);
        $this->assertAttributeContains($printer, "printers", $runner);
    }

    public function test_it_runs_all_jobs()
    {
        $printers = [
            ['name' => "mock_printer", 'url' => "mock_url", "path" => "mock_path"],
            ['name' => "mock_printer2", 'url' => "mock_url2", "path" => "mock_path2"],
        ];

        $mock_factory = Mockery::mock(\Tpavlek\PrintJobs\TaskRunner\TaskFactory::class);

        $runner = new \Tpavlek\PrintJobs\TaskRunner\Runner(
            $printers,
            new \Tpavlek\PrintJobs\PrinterFactory(new Goutte\Client()),
            $mock_factory,
            new \League\Event\Emitter()
        );
        $mock_task = Mockery::mock(\Tpavlek\PrintJobs\TaskRunner\Task::class);
        $mock_factory->shouldReceive('make')->twice()->andReturn($mock_task);
        $mock_task->shouldReceive('run')->twice();

        $runner->run(null, null);
    }

    public function test_it_emits_timed_out_event()
    {
        $printers = [
            ['name' => "mock_printer", 'url' => "mock_url", "path" => "mock_path"],
        ];

        $mock_factory = Mockery::mock(\Tpavlek\PrintJobs\TaskRunner\TaskFactory::class);
        $mock_emitter = Mockery::mock(\League\Event\Emitter::class);

        $runner = new \Tpavlek\PrintJobs\TaskRunner\Runner(
            $printers,
            new \Tpavlek\PrintJobs\PrinterFactory(new Goutte\Client()),
            $mock_factory,
            $mock_emitter
        );

        $mock_factory
            ->shouldReceive('make')
            ->once()
            ->andThrow(\GuzzleHttp\Exception\AdapterException::class);

        $mock_emitter
            ->shouldReceive('emit')
            ->once()
            ->withArgs([
                    Mockery::type(\Tpavlek\PrintJobs\IO\Events\TimedOutEvent::class),
                    Mockery::type(\Tpavlek\PrintJobs\Printer::class)
                ]);

        $runner->run(null, null);
    }

    public function test_it_emits_unknown_error_exception()
    {
        $printers = [
            ['name' => "mock_printer", 'url' => "mock_url", "path" => "mock_path"],
        ];

        $mock_factory = Mockery::mock(\Tpavlek\PrintJobs\TaskRunner\TaskFactory::class);
        $mock_emitter = Mockery::mock(\League\Event\Emitter::class);

        $runner = new \Tpavlek\PrintJobs\TaskRunner\Runner(
            $printers,
            new \Tpavlek\PrintJobs\PrinterFactory(new Goutte\Client()),
            $mock_factory,
            $mock_emitter
        );

        $exception = new \Exception("Mock_exception");

        $mock_factory->shouldReceive('make')->once()->andThrow($exception);
        $mock_emitter
            ->shouldReceive('emit')
            ->once()
            ->withArgs([
                Mockery::type(\Tpavlek\PrintJobs\IO\Events\UnknownErrorEvent::class),
                Mockery::type(UnknownError::class)
            ]);

        $runner->run(null, null);
    }

}
