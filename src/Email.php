<?php

namespace Tpavlek\PrintJobs;

use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;

class Email {

    /** @var Job  */
    protected $job;
    /** @var Printer  */
    protected $printer;
    /** @var SmtpMailer  */
    protected $mailer;
    /** @var Message  */
    protected $message;

    public function __construct(Job $job, Printer $printer, SmtpMailer $mailer, Message $message)
    {
        $this->job = $job;
        $this->printer = $printer;
        $this->mailer = $mailer;
        $this->message = $message;
    }

    public function send(array $to)
    {
        $subject = "Job Stalled on Printer {$this->printer->name}!";
        $body = "Hello citizen, \n There is a stalled job on printer {$this->printer->name}. You can view more at"
            . " {$this->printer->url}";

        $this->message
            ->setFrom("no-reply@ualberta.ca", "UAlberta Printer Agent")
            ->setSubject($subject)
            ->setBody($body);

        foreach ($to as $to_email) {
            $this->message->addTo($to_email);
        }

        $this->mailer->send($this->message);
    }

} 
