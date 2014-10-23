<?php

class PrinterTest extends PHPUnit_Framework_TestCase {

    public function testConstructor() {

    }

    public function testParse()
    {
        $dom = new Symfony\Component\DomCrawler\Crawler(file_get_contents('has_job.html'));
        $mock_client = Mockery::mock('\Goutte\Client');
    }

}
