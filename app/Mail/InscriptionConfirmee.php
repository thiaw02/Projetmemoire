<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InscriptionConfirmee extends Mailable
{
    use Queueable, SerializesModels;

    public $numero_dossier;
    public $email;
    public $password_defaut;

    /**
     * Create a new message instance.
     */
    public function __construct($numero_dossier, $email, $password_defaut)
    {
        $this->numero_dossier = $numero_dossier;
        $this->email = $email;
        $this->password_defaut = $password_defaut;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Confirmation de votre inscription')
                    ->view('emails.inscription_confirmee')
                    ->with([
                        'numero_dossier' => $this->numero_dossier,
                        'email' => $this->email,
                        'password_defaut' => $this->password_defaut,
                    ]);
    }
}
