<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransactionReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $firstName;
    public $transactionId;
    public $transactionType;
    public $amount;
    public $fee;
    public $total;
    public $status;
    public $date;
    public $description;
    public $party;
    public $newBalance;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $firstName,
        string $transactionId,
        string $transactionType,
        float $amount,
        float $fee,
        float $total,
        string $status,
        string $date,
        ?string $description,
        string $party,
        float $newBalance
    ) {
        $this->firstName = $firstName;
        $this->transactionId = $transactionId;
        $this->transactionType = $transactionType;
        $this->amount = $amount;
        $this->fee = $fee;
        $this->total = $total;
        $this->status = $status;
        $this->date = $date;
        $this->description = $description;
        $this->party = $party;
        $this->newBalance = $newBalance;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Transaction Receipt — {$this->transactionType} | ₦" . number_format($this->amount, 2),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.transaction-receipt',
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
