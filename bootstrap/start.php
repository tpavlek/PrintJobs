<?php
use Tpavlek\PrintJobs\PrinterFactory;

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
    $handler = new \Monolog\Handler\StreamHandler('logs/printjobs.html', \Monolog\Logger::INFO);
    $handler->setFormatter(new \Monolog\Formatter\HtmlFormatter());
    $logger->pushHandler($handler);
    return new \Tpavlek\PrintJobs\IO\IO($logger, new \Tpavlek\PrintJobs\IO\Echoer());
});

/*
 * We need to register a singleton emitter because we must guarantee that we are consistently using only ONE emitter.
 */
$container->add('emitter', function() {
    return new League\Event\Emitter();
}, true);

$container
    ->add('taskFactory', \Tpavlek\PrintJobs\TaskRunner\TaskFactory::class)
    ->withArgument('client')
    ->withArgument('io')
    ->withArgument('emitter');

$container
    ->add('printerFactory', PrinterFactory::class)
    ->withArgument('client');

