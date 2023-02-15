<?php

namespace App\Http\Controllers;

use App\Mail\MessageReceived;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MessagesController extends Controller
{
    public function contact() {
        try {
            $message = request()->validate([
                'email' => 'required|email',
                'name' => 'required',
                'subject' => 'required',
                'message' => 'required|min:10|max:255'
            ], [
                'email.required' => 'El correo es obligatorio.',
                'email.email' => 'El correo ingresado no es valido.',
                'name.required' => 'El nombre es obligatorio.',
                'subject.required' => 'El asunto es obligatorio.',
                'message.required' => 'El mensaje es obligatorio.',
                'message.min' => 'El mensaje debe contener al menos 10 caracteres.',
                'message.max' => 'El mensaje no debe ser mayor que 255 caracteres.',
            ]);

            $msg = "
                <p>Recibiste un mensaje de $message[name]</p>
                <p><b>Asunto: </b>$message[subject]</p>
                <p><b>Email: </b>$message[email]</p>
                <p><b>Mensaje: </b>".nl2br($message['message'])."</p>
            ";

            Mail::to(env('MAIL_FROM_ADDRESS'))->send(new MessageReceived($msg, env('APP_NAME').', Mensaje recibido!'));
    
            return response()->json([
                'success' => 'Mensaje enviado exitosamente!',
            ]);
        } catch (\Throwable $th) {
            Log::error($th->__toString());
            return $th->getMessage();
        }
    }

    public function newUser($user, $password) {
        try {
            $msg = "
                <p>Buen día,</p>
                <p align='justify'>El equipo de ".env('APP_NAME')." te saluda. Gracias por confiar en nosotros, a continuación encontraras tus credenciales de ingreso.</p>
                <p align='justify'>
                    <b>Usuario:</b> $user<br>
                    <b>Contraseña:</b> $password
                </p>
                <p align='center'>
                    <strong>Inicia sesión aquí</strong>
                    <br>
                    <br>
                    <a href='".env('APP_URL')."' target='_blank' style='text-decoration: none; background-color: #0578a4; color: white; padding: 10px 50px; border-radius: 10px; font-weight: 800;'>
                        Ingresar
                    </a>
                </p>
            ";
            
            Mail::to($user)
                ->bcc(env('MAIL_FROM_ADDRESS'))
                ->send(new MessageReceived($msg, env('APP_NAME').', Bienvenido!'));
    
            return response()->json([
                'success' => 'Mensaje enviado exitosamente!',
            ]);
        } catch (\Throwable $th) {
            Log::error($th->__toString());
            return $th->getMessage();
        }
    }

    public function changePassword($user, $password) {
        try {
            $msg = "
                <p>Buen día,</p>
                <p align='justify'>Cambio de contraseña exitoso!.</p>
                <p align='justify'>
                    <b>Usuario:</b> $user<br>
                    <b>Contraseña:</b> $password
                </p>
                <p align='center'>
                    <strong>Inicia sesión aquí</strong>
                    <br>
                    <br>
                    <a href='".env('APP_URL')."' target='_blank' style='text-decoration: none; background-color: #0578a4; color: white; padding: 10px 50px; border-radius: 10px; font-weight: 800;'>
                        Ingresar
                    </a>
                </p>
            ";
            
            Mail::to($user)
                ->bcc(env('MAIL_FROM_ADDRESS'))
                ->send(new MessageReceived($msg, env('APP_NAME').', Cambio de contraseña!'));
    
            return response()->json([
                'success' => 'Mensaje enviado exitosamente!',
            ]);
        } catch (\Throwable $th) {
            Log::error($th->__toString());
            return $th->getMessage();
        }
    }
}
