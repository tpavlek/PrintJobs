<?php

class PrinterTest extends PHPUnit_Framework_TestCase {

    /** @var  Mockery\MockInterface */
    protected $mock_goutte;

    public function setUp() {
        $this->mock_goutte = Mockery::mock('\Goutte\Client');
    }

    public function tearDown() {
        Mockery::close();
    }

    public function testConstructor() {
        $printer = new \Tpavlek\PrintJobs\Printer("mock_url", "mock_name", $this->mock_goutte);

        $this->assertAttributeEquals($this->mock_goutte, "client", $printer);
        $this->assertAttributeEquals("mock_name", "name", $printer);
        $this->assertAttributeEquals("mock_url", "url", $printer);
    }

    public function testGetFirstRemoteJob()
    {
        $domCrawler = new Symfony\Component\DomCrawler\Crawler(file_get_contents('tests/files/has_job.html'));
        $this->mock_goutte->shouldReceive('request')->andReturn($domCrawler);

        $printer = new \Tpavlek\PrintJobs\Printer("mock_url", "mock_name", $this->mock_goutte);

        $job = $printer->getFirstRemoteJob();
        $this->assertInstanceOf('\Tpavlek\PrintJobs\Job', $job);
    }

    public function testGetFirstRemoteJobNull() {
        $domCrawler = new Symfony\Component\DomCrawler\Crawler(file_get_contents('tests/files/no_job.html'));
        $this->mock_goutte->shouldReceive('request')->andReturn($domCrawler);

        $printer = new \Tpavlek\PrintJobs\Printer("mock_url", "mock_name", $this->mock_goutte);

        $job = $printer->getFirstRemoteJob();
        $this->assertNull($job);
    }

    public function testGetFilesystem() {
        $printer = new \Tpavlek\PrintJobs\Printer("mock_url", "mock_name", $this->mock_goutte);

        $filesystem = $printer->getFilesystem();

        $this->assertInstanceOf('\Tpavlek\PrintJobs\PrinterFile', $filesystem);
    }

    public function testGetNameFromUrl() {
        $url = "http://129.128.2.17";
        $name = \Tpavlek\PrintJobs\Printer::getNameFromUrl($url);
        $this->assertEquals(17, $name);
    }

    public function test_it_gets_management_url() {
        $url = "http://google.com/test/query?string=test";
        $printer = new \Tpavlek\PrintJobs\Printer($url, "Test Name", $this->mock_goutte);

        $result = $printer->getManagementUrl();
        $expected = "http://google.com/properties/authentication/login.php?redir=/support/remoteUI/RUIViewer.php?login=true";

        $this->assertEquals($expected, $result);
    }




}
