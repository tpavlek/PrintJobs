<?php

require 'vendor/autoload.php';

$jobs_path = "jobs/active.php?tab=jobs";

$printers = [
    "https://129.128.183.8/",
    "https://129.128.183.21/",
    "https://129.128.183.22/",
    "https://129.128.183.51/"
];

$client = new Goutte\Client();

// We want to disable ssl verify because the certs are broken
$guzzle = $client->getClient();
$guzzle->setDefaultOption('verify', false);
$client->setClient($guzzle);

$crawler = $client->request('GET', "https://129.128.183.8/{$jobs_path}");

$div = $crawler->filter('.tableDiv');

print_r($div);


