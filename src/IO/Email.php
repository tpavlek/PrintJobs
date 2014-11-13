<?php

namespace Tpavlek\PrintJobs\IO;

use League\Event\AbstractEvent;
use League\Event\AbstractListener;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use Tpavlek\PrintJobs\IO\Events\SendEmailEvent;
use League\Url\Url;
use Tpavlek\PrintJobs\Printer;

/**
 * Class Email
 *
 * Represents an instance of an Email to send.
 *
 * @package Tpavlek\PrintJobs
 */
class Email extends AbstractListener
{

    use ListenerTrait;

    /** @var SmtpMailer */
    protected $mailer;
    /** @var Message */
    protected $message;
    /** @var array  */
    protected $send_to;

    /**
     * Construct a new Email instance.
     *
     * @param SmtpMailer $mailer
     * @param Message $message
     * @param array $send_to
     */
    public function __construct(SmtpMailer $mailer, Message $message, array $send_to)
    {
        $this->mailer = $mailer;
        $this->message = $message;
        $this->send_to = $send_to;
    }

    /**
     * Send the email.
     *
     * @param PrinterJob $printerJob
     * @throws \Exception
     * @throws \Nette\Mail\SmtpException
     */
    public function send(PrinterJob $printerJob)
    {
        $subject = "Job {$printerJob->job->id} stalled on printer {$printerJob->printer->name}!";
        $body = "Hello citizen," . PHP_EOL .
            "Job {$printerJob->job->id} is stalled on printer {$printerJob->printer->name}." . PHP_EOL .
            "You can view the list of jobs at {$printerJob->printer->url}" . PHP_EOL . PHP_EOL .
            "You can view the managment interface at: " . $printerJob->printer->getManagementUrl();

        $this->message
            ->setFrom("no-reply@ualberta.ca", "UAlberta Printer Agent")
            ->setSubject($subject)
            ->setBody($body);

        foreach ($this->send_to as $to_email) {
            $this->message->addTo($to_email);
        }

        $this->mailer->send($this->message);
    }

    /**
     * Handle an event.
     *
     * @param AbstractEvent $event
     *
     * @return void
     */
    public function handle(AbstractEvent $event, $param = null)
    {
        if ($event instanceof SendEmailEvent) {
            $this->checkParam($param, $event, PrinterJob::class);
            $this->send($param);
        }
    }
}
