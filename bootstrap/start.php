<?php
require 'vendor/autoload.php';

$container = new \Orno\Di\Container();

$container->add('mailer', '\Nette\Mail\SmtpMailer')
    ->withArgument([
        'host' => 'smtp.srv.ualberta.ca',
        'username' => 'glados@ualberta.ca'
    ]);

$container->add('message', '\Nette\Mail\Message');

