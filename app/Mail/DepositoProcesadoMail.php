<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DepositoProcesadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data; // Datos que pasarás al correo

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data; // Asignar datos al correo
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.depositoProcesado') // Vista del correo
                    ->subject('Deposito procesado exitosamente') // Asunto
                    ->with('data', $this->data); // Pasar datos a la vista
    }
}
