<?php

namespace Tpavlek\PrintJobs;

use Symfony\Component\DomCrawler\Crawler;

class Job {

    public $id;
    public $name;
    public $owner;
    public $status;
    public $type;
    public $copy_count;

    public function __construct($id, $name, $owner, $status, $type, $copy_count) {
        $this->id = $id;
        $this->name = $name;
        $this->owner = $owner;
        $this->status = $status;
        $this->type = $type;
        $this->copy_count = $copy_count;
    }

    public function hash() {
        return hash("sha256", $this->name . $this->owner . $this->status . $this->type . $this->copy_count);
    }

    public static function parseFromDom(Crawler $crawler) {
        $children = $crawler->children();

        // If the entire content of the row is "No Jobs", then we can't process a job, return null.
        if (trim($crawler->getNode(0)->textContent) == "No Jobs") {
            return null;
        }

        // The ID of the row attribute contains 'job:824' where 824 is the job number.
        $id = explode(":", $crawler->getNode(0)->getAttribute('id'))[1];
        // The copy count is followed by "gDefaultRow". We only want the integer preceeding it.
        $copy_count = explode("g", $children->getNode(4)->textContent)[0];
        return new Job(
            $id,
            trim($children->getNode(0)->textContent),
            trim($children->getNode(1)->textContent),
            trim($children->getNode(2)->textContent),
            trim($children->getNode(3)->textContent),
            trim($copy_count)
        );
    }

} 
