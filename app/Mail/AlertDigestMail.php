<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class AlertDigestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /** @param Collection<int, \App\Models\AlertTrigger> $triggers */
    public function __construct(
        public User $user,
        public Collection $triggers,
    ) {}

    public function envelope(): Envelope
    {
        $count = $this->triggers->count();

        return new Envelope(
            subject: $count === 1
                ? '1 watchlist alert triggered'
                : "{$count} watchlist alerts triggered",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.alert-digest',
            with: ['user' => $this->user, 'triggers' => $this->triggers],
        );
    }
}
