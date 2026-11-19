<?php

namespace App\Mail;

use App\Mail\Concerns\EnvoiEnFileAttente;
use App\Models\DemandeOrientation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Envoyé en arrière-plan : la demande est enregistrée même si le serveur SMTP est en panne. */
class NouvelleDemandeOrientation extends Mailable implements ShouldQueue
{
    use EnvoiEnFileAttente, Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public DemandeOrientation $demande)
    {
        $this->afterCommit();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle demande d\'orientation — JeCMAP',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.demande-orientation',
            with: [
                'demande' => $this->demande->load('beneficiaire'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
