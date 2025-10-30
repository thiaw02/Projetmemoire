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
    public $role;
    public $created_at;

    /**
     * Create a new message instance.
     */
    public function __construct($numero_dossier, $email, $password_defaut, $role = null, $created_at = null)
    {
        $this->numero_dossier = $numero_dossier;
        $this->email = $email;
        $this->password_defaut = $password_defaut;
        $this->role = $role;
        $this->created_at = $created_at;
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
                        'role' => $this->role,
                        'created_at' => $this->created_at,
                    ]);
    }
}
