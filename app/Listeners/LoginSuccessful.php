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
            $id_parametro_super_administrador = isset(TblParametro::where(['llave' => 'id_dominio_super_administrador', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_super_administrador', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_administrador = isset(TblParametro::where(['llave' => 'id_dominio_administrador', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_administrador', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_cliente = isset(TblParametro::where(['llave' => 'id_dominio_cliente', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_cliente', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_proveedor = isset(TblParametro::where(['llave' => 'id_dominio_proveedor', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_proveedor', 'estado' => 1])->first()->valor
                : 0;

            $id_parametro_terceros = isset(TblParametro::where(['llave' => 'id_dominio_tipo_tercero', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_tipo_tercero', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_documentos = isset(TblParametro::where(['llave' => 'id_dominio_tipo_documento', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_tipo_documento', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_plantilla_correo = isset(TblParametro::where(['llave' => 'id_dominio_plantilla_correo', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_plantilla_correo', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_plantilla_correo_default = isset(TblParametro::where(['llave' => 'id_dominio_plantilla_correo_default', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_plantilla_correo_default', 'estado' => 1])->first()->valor
                : 0;
            $id_parametro_impuestos = isset(TblParametro::where(['llave' => 'id_dominio_impuestos', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_impuestos', 'estado' => 1])->first()->valor
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
            $id_parametros_mano_obra = isset(TblParametro::where(['llave' => 'id_dominio_mano_obra', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_mano_obra', 'estado' => 1])->first()->valor
                : 0;
            $id_parametros_materiales = isset(TblParametro::where(['llave' => 'id_dominio_materiales', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_materiales', 'estado' => 1])->first()->valor
                : 0;
            $id_parametros_transporte = isset(TblParametro::where(['llave' => 'id_dominio_transporte', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_transporte', 'estado' => 1])->first()->valor
                : 0;
            $id_parametros_tipos_trabajo = isset(TblParametro::where(['llave' => 'id_dominio_tipos_trabajo', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_tipos_trabajo', 'estado' => 1])->first()->valor
                : 0;
            $id_parametros_tipos_prioridad = isset(TblParametro::where(['llave' => 'id_dominio_tipos_prioridad', 'estado' => 1])->first()->valor)
                ? TblParametro::where(['llave' => 'id_dominio_tipos_prioridad', 'estado' => 1])->first()->valor
                : 0;
            
            Session::put('perfil', $usuario->tbltercero->tbldominiotercero->nombre);
            Session::put('residencia', $usuario->tbltercero->ciudad);

            Session::put('id_dominio_super_administrador', intval($id_parametro_super_administrador));
            Session::put('id_dominio_administrador', intval($id_parametro_administrador));
            Session::put('id_dominio_cliente', intval($id_parametro_cliente));
            Session::put('id_dominio_proveedor', intval($id_parametro_proveedor));

            Session::put('id_dominio_tipo_tercero', intval($id_parametro_terceros));
            Session::put('id_dominio_tipo_documento', intval($id_parametro_documentos));

            Session::put('id_dominio_plantilla_correo', intval($id_parametro_plantilla_correo));
            Session::put('id_dominio_plantilla_correo_default', intval($id_parametro_plantilla_correo_default));

            Session::put('id_dominio_zonas', intval($id_parametro_zonas));
            Session::put('id_dominio_transportes', intval($id_parametro_transportes));
            Session::put('id_dominio_accesos', intval($id_parametro_accesos));
            Session::put('id_dominio_tipo_items', intval($id_parametro_tipo_items));
            Session::put('id_dominio_impuestos', intval($id_parametro_impuestos));

            Session::put('id_dominio_mano_obra', intval($id_parametros_mano_obra));
            Session::put('id_dominio_materiales', intval($id_parametros_materiales));
            Session::put('id_dominio_transporte', intval($id_parametros_transporte));
            Session::put('id_dominio_tipos_trabajo', intval($id_parametros_tipos_trabajo));
            Session::put('id_dominio_tipos_prioridad', intval($id_parametros_tipos_prioridad));
        }
    }
}