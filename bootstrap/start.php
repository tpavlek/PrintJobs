<?php
require 'vendor/autoload.php';
require 'config/config.php';

$container = new \Orno\Di\Container();

$container->add('mailer', '\Nette\Mail\SmtpMailer')
    ->withArgument([
        'host' => 'smtp.srv.ualberta.ca',
        'username' => 'glados@ualberta.ca'
    ]);

$container->add('message', '\Nette\Mail\Message');

$container->add('client', function() {
    $client = new \Goutte\Client();

    // We must set SSL to noverify, as the SSL certs are not properly set on the server.
    $guzzle = $client->getClient();
    $guzzle->setDefaultOption('verify', false);
    $client->setClient($guzzle);
    return $client;
});

$container->add('io', function() {
    $logger = new \Monolog\Logger('log');
    $logger->pushHandler(new \Monolog\Handler\StreamHandler('logs/printjobs.log', \Monolog\Logger::INFO));
    return new \Tpavlek\PrintJobs\IO($logger);
});

