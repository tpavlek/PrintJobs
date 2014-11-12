<?php

namespace Tpavlek\PrintJobs\TaskRunner;

use Tpavlek\PrintJobs\IO\IO;

class Runner
{

    protected $printers;
    /** @var TaskFactory */
    protected $factory;
    protected $io;

    /**
     * Construct a new Runner instance.
     * @param array $printers An array of stdClass representations of the printers to process.
     * @param TaskFactory $factory A factory to instantiate new tasks
     * @param \Tpavlek\PrintJobs\IO\IO $io
     */
    public function __construct(array $printers, TaskFactory $factory, IO $io)
    {
        $this->printers = $printers;
        $this->factory = $factory;
        $this->io = $io;
    }

    public function run()
    {
        foreach ($this->printers as $printer) {
            try {
                $this->factory->make($printer, $this->io)->run(null, null);
            } catch (\GuzzleHttp\Exception\AdapterException $exception) {
                $this->io->error("-- \n Timed out connecting to printer: {$printer->name}\n");
            } catch (\Exception $exception) {
                var_dump($exception->getMessage());
                $this->io->error("The following error occurred: '" . $exception->getMessage() . "' on printer {$printer->name}\n");
            }
        }
    }

} 
