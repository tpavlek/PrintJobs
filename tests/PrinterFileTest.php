<?php

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



}
