<?php

namespace App\Mail;

use App\Models\ContactInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ContactInquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactInquiry $inquiry)
    {
        $this->inquiry->loadMissing('offer');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ny henvendelse fra kontaktformular: '.$this->inquiry->name,
            replyTo: [
                $this->inquiry->email => $this->inquiry->name,
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.contact-inquiry',
            with: [
                'inquiry' => $this->inquiry,
                'holdStartLabel' => $this->holdStartLabel(),
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    private function holdStartLabel(): ?string
    {
        $value = $this->inquiry->preferred_hold_start;
        if ($value === null || $value === '') {
            return null;
        }

        $options = Collection::make(config('marketing.hold_start_options', []));

        return $options->firstWhere('value', $value)['label'] ?? $value;
    }
}
