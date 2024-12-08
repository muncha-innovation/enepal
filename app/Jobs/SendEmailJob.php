<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $subject;
    public $body;
    public $fromEmail;
    public $fromName;
    public $recipientEmail;
    public $recipientName;

    /**
     * Create a new job instance.
     */
    public function __construct($subject, $body, $fromEmail, $fromName, $recipientEmail, $recipientName)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->recipientEmail = $recipientEmail;
        $this->recipientName = $recipientName;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Mail::send([], [], function ($message) {
            $message->to($this->recipientEmail, $this->recipientName)
                ->subject($this->subject)
                ->from($this->fromEmail, $this->fromName)
                ->setBody($this->body, 'text/html');
        });
    }
}
