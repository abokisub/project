<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $firstName;
    public $otpCode;
    public $expiresIn;
    public $ipAddress;
    public $location;
    public $browser;

    /**
     * Create a new message instance.
     */
    public function __construct(string $firstName, string $otpCode, int $expiresIn, string $ipAddress, string $location, string $browser)
    {
        $this->firstName = $firstName;
        $this->otpCode = $otpCode;
        $this->expiresIn = $expiresIn;
        $this->ipAddress = $ipAddress;
        $this->location = $location;
        $this->browser = $browser;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your KoboPoint Login Code',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.login-otp',
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
