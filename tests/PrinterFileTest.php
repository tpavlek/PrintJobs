<?php

use Tpavlek\PrintJobs\JobData;

class PrinterFileTest extends PHPUnit_Framework_TestCase {

    protected $path;
    protected $filename;
    protected $printer_name;


    public function setUp() {
        $this->path = "tests/files/";
        $this->filename = "printer-1.json";
        $this->printer_name = 1;
        if (file_exists($this->path . $this->filename)) {
            unlink($this->path . $this->filename);
        }

    }

    public function testConstructor() {
        $file = new \Tpavlek\PrintJobs\PrinterFile($this->printer_name, $this->path);

        $this->assertAttributeEquals("{$this->path}printer-{$this->printer_name}.json", "filename", $file);
        $this->assertFileExists("tests/files/printer-1.json");
    }

    public function testClear() {
        file_put_contents($this->path . $this->filename, "Sample Text");
        $file = new \Tpavlek\PrintJobs\PrinterFile($this->printer_name, $this->path);

        $file->clear();

        $this->assertEquals("{}", file_get_contents($this->path . $this->filename));
    }

    public function testLoad() {
        $data = [
            'hash' => "mock_hash",
            "date" => (string)Carbon\Carbon::now(),
            'email_sent' => false
        ];

        file_put_contents($this->path . $this->filename, json_encode($data));

        $file = new \Tpavlek\PrintJobs\PrinterFile($this->printer_name, $this->path);
        $job_data = $file->load();

        $this->assertInstanceOf(JobData::class, $job_data);
        $this->assertEquals($data['hash'], $job_data->hash);
        $this->assertInstanceOf(Carbon\Carbon::class, $job_data->date);
        $this->assertEquals($data['date'], (string)$job_data->date);
        $this->assertEquals($data['email_sent'], $job_data->email_sent);
    }



}
