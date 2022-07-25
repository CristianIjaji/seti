<?php

namespace App\Listeners;

use App\Models\TblParametro;
use App\Models\TblUsuario;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginSuccessful
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $usuario = TblUsuario::with('tbltercero')->where('estado', 1)->find($event->user->id_usuario);

        if(!$usuario) {
            Auth::logout();
            return redirect('/');
        } else {
            $parametros = TblParametro::where('estado', '=', 1)->get();
            foreach ($parametros as $parametro) {
                Session::put($parametro->llave, intval($parametro->valor));
            }
        }
    }
}