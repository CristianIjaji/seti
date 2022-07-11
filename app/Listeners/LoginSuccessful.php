<?php

namespace App\Listeners;

use App\Models\TblParametro;
use App\Models\TblUsuario;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Support\Facades\Auth;
use Session;

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
            $id_super_administrador = isset(TblParametro::where(['llave' => 'id_dominio_super_administrador', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_super_administrador', 'estado' => 1])->first()->valor
                : 0;
            $id_administrador = isset(TblParametro::where(['llave' => 'id_dominio_administrador', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_administrador', 'estado' => 1])->first()->valor
                : 0;
            $id_agente = isset(TblParametro::where(['llave' => 'id_dominio_agente', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_agente', 'estado' => 1])->first()->valor
                : 0;
            $id_asociado = isset(TblParametro::where(['llave' => 'id_dominio_asociado', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_asociado', 'estado' => 1])->first()->valor
                : 0;

            $id_parametro_terceros = isset(TblParametro::where(['llave' => 'id_dominio_tipo_tercero', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_tipo_tercero', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_documentos = isset(TblParametro::where(['llave' => 'id_dominio_tipo_documento', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_tipo_documento', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_orden = isset(TblParametro::where(['llave' => 'id_dominio_tipo_orden', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_tipo_orden', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_domicilio = isset(TblParametro::where(['llave' => 'id_tipo_orden_domicilio', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_tipo_orden_domicilio', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_reserva_hotel = isset(TblParametro::where(['llave' => 'id_tipo_orden_reserva_hotel', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_tipo_orden_reserva_hotel', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_reserva_restaurante = isset(TblParametro::where(['llave' => 'id_tipo_orden_reserva_restaurante', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_tipo_orden_reserva_restaurante', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_plantilla_correo = isset(TblParametro::where(['llave' => 'id_dominio_plantilla_correo', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_plantilla_correo', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_plantilla_recibo = isset(TblParametro::where(['llave' => 'id_dominio_plantilla_recibo', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_plantilla_recibo', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_plantilla_correo_default = isset(TblParametro::where(['llave' => 'id_dominio_plantilla_correo_default', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_plantilla_correo_default', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_plantilla_recibo_default = isset(TblParametro::where(['llave' => 'id_dominio_plantilla_recibo_default', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_plantilla_recibo_default', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_tiempos_domicilio = isset(TblParametro::where(['llave' => 'id_dominio_tiempos_domicilio', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_tiempos_domicilio', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_estados_orden = isset(TblParametro::where(['llave' => 'id_dominio_estados_orden', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_estados_orden', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_orden_rechazada = isset(TblParametro::where(['llave' => 'id_dominio_orden_rechazada', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_orden_rechazada', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_dominio_orden_cola = isset(TblParametro::where(['llave' => 'id_dominio_orden_cola', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_orden_cola', 'estado' => 1])->first()->valor
                : 0;
            $id_dominio_orden_aceptada = isset(TblParametro::where(['llave' => 'id_dominio_orden_aceptada', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_orden_aceptada', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_orden_camino = isset(TblParametro::where(['llave' => 'id_dominio_orden_camino', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_orden_camino', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_orden_entregada = isset(TblParametro::where(['llave' => 'id_dominio_orden_entregada', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_orden_entregada', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_orden_devuelta = isset(TblParametro::where(['llave' => 'id_dominio_orden_devuelta', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_orden_devuelta', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_orden_aceptada_domiciliario = isset(TblParametro::where(['llave' => 'id_dominio_orden_aceptada_domiciliario', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_orden_aceptada_domiciliario', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_orden_completada = isset(TblParametro::where(['llave' => 'id_dominio_orden_completada', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_orden_completada', 'estado' => 1])->first()->valor
                : 0;
            
            $id_parametro_zonas = isset(TblParametro::where(['llave' => 'id_dominio_zonas', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_zonas', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_transportes = isset(TblParametro::where(['llave' => 'id_dominio_transportes', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_transportes', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_accesos = isset(TblParametro::where(['llave' => 'id_dominio_accesos', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_accesos', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_tipo_items = isset(TblParametro::where(['llave' => 'id_dominio_tipo_items', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_tipo_items', 'estado' => 1])->first()->valor
                : 0;
            
            Session::put('perfil', $usuario->tbltercero->tbldominiotercero->nombre);
            Session::put('residencia', $usuario->tbltercero->ciudad);

            Session::put('id_dominio_super_administrador', intval($id_super_administrador));
            Session::put('id_dominio_administrador', intval($id_administrador));
            Session::put('id_dominio_agente', intval($id_agente));
            Session::put('id_dominio_asociado', intval($id_asociado));

            Session::put('id_dominio_tipo_tercero', intval($id_parametro_terceros));
            Session::put('id_dominio_tipo_documento', intval($id_parametro_documentos));
            Session::put('id_dominio_tipo_orden', intval($id_parametro_orden));
            Session::put('id_dominio_domicilio', intval($id_parametro_domicilio));
            Session::put('id_dominio_reserva_hotel', intval($id_parametro_reserva_hotel));
            Session::put('id_dominio_reserva_restaurante', intval($id_parametro_reserva_restaurante));

            Session::put('id_dominio_plantilla_correo', intval($id_parametro_plantilla_correo));
            Session::put('id_dominio_plantilla_recibo', intval($id_parametro_plantilla_recibo));
            Session::put('id_dominio_plantilla_correo_default', intval($id_parametro_plantilla_correo_default));
            Session::put('id_dominio_plantilla_recibo_default', intval($id_parametro_plantilla_recibo_default));

            Session::put('id_dominio_zonas', intval($id_parametro_zonas));
            Session::put('id_dominio_transportes', intval($id_parametro_transportes));
            Session::put('id_dominio_accesos', intval($id_parametro_accesos));
            Session::put('id_dominio_tipo_items', intval($id_parametro_tipo_items));



            Session::put('id_dominio_tiempos_domicilio', intval($id_parametro_tiempos_domicilio));

            Session::put('id_dominio_estados_orden', intval($id_parametro_estados_orden));
            Session::put('id_dominio_orden_cola', intval($id_parametro_dominio_orden_cola));
            Session::put('id_dominio_orden_aceptada', intval($id_dominio_orden_aceptada));
            Session::put('id_dominio_orden_rechazada', intval($id_parametro_orden_rechazada));
            Session::put('id_dominio_orden_camino', intval($id_parametro_orden_camino));
            Session::put('id_dominio_orden_entregada', intval($id_parametro_orden_entregada));
            Session::put('id_dominio_orden_devuelta', intval($id_parametro_orden_devuelta));
            Session::put('id_dominio_orden_aceptada_domiciliario', intval($id_parametro_orden_aceptada_domiciliario));
            Session::put('id_dominio_orden_completada', intval($id_parametro_orden_completada));
        }
    }
}
