<?php

class JobTest extends PHPUnit_Framework_TestCase {

    public function testParseWithJobs() {
        $html = file_get_contents('tests/files/has_job.html');
        $dom = new Symfony\Component\DomCrawler\Crawler($html);
        $filtered = $dom->filter(\Tpavlek\PrintJobs\Printer::REMOTE_TABLE_NAME . "> tbody tr")->first();
        $job = \Tpavlek\PrintJobs\Job::parseFromDom($filtered);

        $this->assertAttributeEquals(952, 'id', $job);
        $this->assertAttributeEquals("Microsoft Word - Midterm Exam_488686 F14", "name", $job);
        $this->assertAttributeEquals("dalejohn", "owner", $job);
        $this->assertAttributeEquals("Printing", "status", $job);
        $this->assertAttributeEquals("print", "type", $job);
        $this->assertAttributeEquals("30", "copy_count", $job);
    }

    public function testParseWithoutJobs() {
        $html = file_get_contents('tests/files/no_job.html');
        $dom = new \Symfony\Component\DomCrawler\Crawler($html);
        $filtered = $dom->filter(\Tpavlek\PrintJobs\Printer::REMOTE_TABLE_NAME . "> tbody tr")->first();

        $job = \Tpavlek\PrintJobs\Job::parseFromDom($filtered);

        $this->assertNull($job);
    }

    public function testHash() {
        $job = new \Tpavlek\PrintJobs\Job(1, "mock_name", "mock_owner", "mock_status", "mock_type", 1);

        $hash = "696deffcb9645a216e8e3529ea36197d0dfbfc63601a3b6d72d21ca1977945c3";
        $this->assertEquals($hash, (string)$job->hash());
    }

}
