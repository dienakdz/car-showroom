<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $activationUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác thực và kích hoạt tài khoản - ' . config('app.name', 'MD-CARS Showroom'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.customer-activation',
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
