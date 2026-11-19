<?php

namespace App\Mail;

use App\Mail\Concerns\EnvoiEnFileAttente;
use App\Models\DemandeOrientation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Envoyé en arrière-plan : la demande est enregistrée même si le serveur SMTP est en panne. */
class ReponseOrientation extends Mailable implements ShouldQueue
{
    use EnvoiEnFileAttente, Queueable, SerializesModels;

    public function __construct(public DemandeOrientation $demande)
    {
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Réponse à ta demande d\'orientation — JeCMAP');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reponse-orientation',
            with: ['demande' => $this->demande->load('beneficiaire')],
        );
    }
}
