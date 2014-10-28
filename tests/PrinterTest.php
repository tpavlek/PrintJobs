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
        $mock_guzzle = Mockery::mock('\GuzzleHttp\Client');
        $this->mock_goutte->shouldReceive('getClient', 'setClient')->andReturn($mock_guzzle);
        $mock_guzzle->shouldReceive('setDefaultOption')->andReturnSelf();
        $printer = new \Tpavlek\PrintJobs\Printer("mock_url", "mock_name", $this->mock_goutte);

        $this->assertAttributeEquals($this->mock_goutte, "client", $printer);
        $this->assertAttributeEquals("mock_name", "name", $printer);
        $this->assertAttributeEquals("mock_url", "url", $printer);
    }

    public function testGetFirstRemoteJob()
    {
        $domCrawler = new Symfony\Component\DomCrawler\Crawler(file_get_contents('tests/files/has_job.html'));
        $this->mock_goutte->shouldReceive('request')->andReturn($domCrawler);

        $mock_guzzle = Mockery::mock('\GuzzleHttp\Client');
        $this->mock_goutte->shouldReceive('getClient', 'setClient')->andReturn($mock_guzzle);
        $mock_guzzle->shouldReceive('setDefaultOption')->andReturnSelf();
        $printer = new \Tpavlek\PrintJobs\Printer("mock_url", "mock_name", $this->mock_goutte);

        $job = $printer->getFirstRemoteJob();
    }

}
