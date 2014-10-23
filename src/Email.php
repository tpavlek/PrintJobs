<?php

namespace Tpavlek\PrintJobs;

class Email {

    const EMAIL_TO = "tpavlek@ualberta.ca";

    protected $job;
    protected $printer;
    protected $transport;
    protected $message;

    public function __construct(Job $job, Printer $printer, \Swift_SmtpTransport $transport, \Swift_Message $message)
    {
        $this->job = $job;
        $this->printer = $printer;
        $transport->setHost('smtp.srv.ualberta.ca');
        $transport->setPort(25);
        $this->transport = $transport;
        $this->message = $message;
    }

    public function send()
    {
        $subject = "Job Stalled on Printer {$this->printer->name}!";
        $body = "Hello citizen, \n There is a stalled job on printer {$this->printer->name}. You can view more at"
            . " {$this->printer->url}";

        $this->message->setSubject($subject)
            ->setFrom([ 'glados@ualberta.ca' => "GLaDOS Printer Agent" ])
            ->setTo([ self::EMAIL_TO ])
            ->setBody($body);

        $result = $this->transport->send($this->message);
    }

} 
