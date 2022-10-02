<?php

namespace App\Policies;

use App\Models\TblActividad;
use App\Models\TblCotizacion;
use App\Models\TblUsuario;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class TblCotizacionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(TblUsuario $tblUsuario)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblCotizacion  $tblCotizacion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion)
    {
        return isset($tblUsuario->getPermisosMenu('quotes.index')->view) ? $tblUsuario->getPermisosMenu('quotes.index')->view : false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(TblUsuario $tblUsuario)
    {
        return isset($tblUsuario->getPermisosMenu('quotes.index')->create) ? $tblUsuario->getPermisosMenu('quotes.index')->create : false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblCotizacion  $tblCotizacion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion)
    {
        return isset($tblUsuario->getPermisosMenu('quotes.index')->update) ? $tblUsuario->getPermisosMenu('quotes.index')->update : false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblCotizacion  $tblCotizacion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblCotizacion  $tblCotizacion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\TblUsuario  $tblUsuario
     * @param  \App\Models\TblCotizacion  $tblCotizacion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion)
    {
        //
    }

    public function export(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion) {
        return isset($tblUsuario->getPermisosMenu('quotes.index')->export) ? $tblUsuario->getPermisosMenu('quotes.index')->export : false;
    }

    public function import(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion) {
        return isset($tblUsuario->getPermisosMenu('quotes.index')->import) ? $tblUsuario->getPermisosMenu('quotes.index')->import : false;
    }


    public function cancelQuote(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion) {
        if(in_array($tblCotizacion->estado, [session('id_dominio_cotizacion_rechazada')])) {
            return false;
        }

        return true;
    }

    public function checkQuote(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion) {
        if(!in_array($tblCotizacion->estado, [session('id_dominio_cotizacion_creada')]) || $tblCotizacion->id_responsable_cliente != $tblUsuario->tbltercero->id_tercero) {
            return false;
        }

        return true;
    }

    public function denyQuote(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion) {
        if(!in_array($tblCotizacion->estado, [session('id_dominio_cotizacion_creada')]) || $tblCotizacion->id_responsable_cliente != $tblUsuario->tbltercero->id_tercero) {
            return false;
        }

        return true;
    }

    public function waitQuote(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion) {
        if(!in_array($tblCotizacion->estado, [session('id_dominio_cotizacion_revisada')])) {
            return false;
        }

        return true;
    }

    public function rejectQuote(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion) {
        if(!in_array($tblCotizacion->estado, [session('id_dominio_cotizacion_pendiente_aprobacion')])) {
            return false;
        }

        return true;
    }

    public function aproveQuote(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion) {
        if(!in_array($tblCotizacion->estado, [session('id_dominio_cotizacion_pendiente_aprobacion')])) {
            return false;
        }

        return true;
    }

    public function createComment(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion) {
        // Se valida sí ya existe una actividad asociado
        $actividad = TblActividad::where(['id_cotizacion' => $tblCotizacion->id_cotizacion])->first();

        if(in_array($tblCotizacion->estado, [session('id_dominio_cotizacion_cancelada')]) || isset($actividad->id_actividad)) {
            return false;
        }

        return true;
    }

    public function createActivity(TblUsuario $tblUsuario, TblCotizacion $tblCotizacion) {
        // Se valida sí ya existe una actividad asociado
        $actividad = TblActividad::where(['id_cotizacion' => $tblCotizacion->id_cotizacion])->first();

        if($tblCotizacion->estado != session('id_dominio_cotizacion_aprobada') || isset($actividad->id_actividad)) {
            return false;
        }

        return isset($tblUsuario->getPermisosMenu('activities.index')->create) ? $tblUsuario->getPermisosMenu('activities.index')->create : false;
    }
}
