<?php
use Tpavlek\PrintJobs\IO\Email;
use Tpavlek\PrintJobs\PrinterFactory;
use Illuminate\Database\Capsule\Manager as Capsule;

require 'vendor/autoload.php';
require 'config/config.php';

$container = new \Orno\Di\Container();

$container->add('mailer', '\Nette\Mail\SmtpMailer')
    ->withArgument([
        'host' => 'smtp.srv.ualberta.ca',
        'username' => 'glados@ualberta.ca'
    ]);

$container->add('message', \Nette\Mail\Message::class);

$container->add('email', Email::class)
    ->withArgument('mailer')
    ->withArgument('message')
    ->withArgument($printjobs_config['send_to']);

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
    ->withArgument('emitter');

$container
    ->add('printerFactory', PrinterFactory::class)
    ->withArgument('client');

/* Prepare the database */
$capsule = new Capsule;
$capsule->addConnection($printjobs_config['db'], 'default');

$container->add('database', Tpavlek\PrintJobs\IO\Database::class)
    ->withArgument($capsule);
