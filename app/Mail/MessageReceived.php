<?php

namespace App\Mail;

use App\Models\TblDominio;
use App\Models\TblParametro;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MessageReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $msg;
    public $sub;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message, $subject)
    {
        $this->msg = $message;
        $this->sub = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $id_dominio_plantilla_correo_default = isset(TblParametro::where(['llave' => 'id_dominio_plantilla_correo_default', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_plantilla_correo_default', 'estado' => 1])->first()->valor
                : 0;

        $plantilla = TblDominio::find($id_dominio_plantilla_correo_default)->descripcion;
        $plantilla = str_replace(['$body', '$year'], [$this->msg, date('Y')], $plantilla);

        return $this->subject($this->sub)->view('emails.message-received', ['plantilla' => $plantilla]);
    }
}
