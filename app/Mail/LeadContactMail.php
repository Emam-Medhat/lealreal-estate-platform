<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $messageContent;
    public $senderName;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $messageContent, $senderName)
    {
        $this->subject = $subject;
        $this->messageContent = $messageContent;
        $this->senderName = $senderName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.leads.contact',
            with: [
                'messageContent' => $this->messageContent,
                'senderName' => $this->senderName,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
